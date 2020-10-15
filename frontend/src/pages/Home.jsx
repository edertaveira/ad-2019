import React, { useEffect, useState } from "react";
import { withRouter } from "react-router-dom";
import axios from "axios";
import { Button, Form, Input, message, Modal, Popconfirm, Space, Table, Typography } from "antd";
import { FaPencilAlt, FaTrash } from "react-icons/fa";
import { UserAddOutlined, ThunderboltOutlined } from "@ant-design/icons";
import "./Home.scss";

const Home = () => {
  const [form] = Form.useForm();
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(false);
  const [loadingRaffle, setLoadingRaffle] = useState(false);
  const [loadingSave, setLoadingSave] = useState(false);
  const [visible, setVisible] = useState(false);
  const [user, setUser] = useState(null);

  useEffect(() => {
    getUsers();
  }, []);

  async function getUsers() {
    setLoading(true);

    const res = await axios.get(`${process.env.REACT_APP_API}/user`);
    if (res.data.success) {
      setUsers(res.data.users);
    }
    setLoading(false);
  }

  const columns = [
    {
      title: "Nome",
      dataIndex: "name",
      key: "name",
    },
    {
      title: "Email",
      dataIndex: "email",
      key: "email",
    },
    {
      title: "Ações",
      render: (_, row) => {
        return (
          <Space>
            <Popconfirm title="Confirma a exclusão des usuário?" onConfirm={() => deleteUser(row._id)}>
              <Button icon={<FaTrash />} type="link" />
            </Popconfirm>
            <Button
              icon={<FaPencilAlt />}
              type="link"
              onClick={() => {
                setUser(row);
                setVisible(true);
                form.setFieldsValue({
                  name: row.name,
                  email: row.email,
                });
              }}
            />
          </Space>
        );
      },
    },
  ];

  async function doRaffle() {
    setLoadingRaffle(true);
    try {
      if (users.length > 0) {
        const res = await axios.get(`${process.env.REACT_APP_API}/raffle`);
        if (res.data.success) message.success("Sorteio Realizado e enviado parao email!", 10);
        else message.error("Erro Inesperado! Tente Novamente mais tarde.");
      } else {
        message.error("Não há usuários para sorteio");
      }
    } catch (e) {
      console.error(e);
      message.error("Não foi possível realizar o sorteio.");
    }
    setLoadingRaffle(false);
  }

  async function deleteUser(id) {
    try {
      const res = await axios.delete(`${process.env.REACT_APP_API}/user/${id}`);
      if (res.data.success) {
        message.success("Usuário Deletado!");
        getUsers();
      } else if (res.data.message) {
        message.error(res.data.message);
      }
    } catch (e) {
      console.error(e);
      message.error("Não foi possível deletar o usuário.");
    }
  }

  async function editUser(user, id) {
    setLoadingSave(true);
    try {
      const res = await axios.put(`${process.env.REACT_APP_API}/user/${id}`, user);
      if (res.data.success) {
        message.success("Usuário Deletado!");
        setVisible(false);
        getUsers();
      } else if (res.data.message) {
        message.error(res.data.message);
      }
    } catch (e) {
      console.error(e);
      message.error("Não foi possível editar o usuário.");
    }
    setLoadingSave(false);
  }

  async function storeUser(user) {
    setLoadingSave(true);
    try {
      const res = await axios.post(`${process.env.REACT_APP_API}/user/store`, user);
      if (res.data.success) {
        message.success("Usuário Cadastrado!");
        setVisible(false);
        getUsers();
      }
    } catch (e) {
      console.error(e);
      message.error("Não foi possível cadastrar o usuário.");
    }
    setLoadingSave(false);
  }

  return (
    <div className="home">
      <Typography.Title level={2}>Amigo Secreto</Typography.Title>

      <div className="btns-top">
        <Button
          size="small"
          type="dashed"
          onClick={() => {
            setUser(null);
            setVisible(true);
            form.resetFields();
          }}
          icon={<UserAddOutlined />}
        >
          Novo Usuário
        </Button>
      </div>

      <Table columns={columns} rowKey="_id" loading={loading} dataSource={users} size="small" />
      <div className="btns-bottom">
        <Button icon={<ThunderboltOutlined />} onClick={doRaffle} size="large" type="primary" loading={loadingRaffle}>
          Realizar Sorteio
        </Button>
      </div>

      <Modal
        title={user ? `Editando ${user.name}` : "Novo Usuário"}
        onCancel={() => setVisible(false)}
        onOk={() => {
          form.validateFields().then((values) => {
            if (user) {
              editUser(values, user._id);
            } else {
              storeUser(values);
            }
          });
        }}
        okButtonProps={{
          loading: loadingSave,
        }}
        visible={visible}
      >
        <Form form={form}>
          <Form.Item name="name" rules={[{ required: true, message: "Preencha o nome." }]}>
            <Input placeholder="Nome" />
          </Form.Item>
          <Form.Item
            name="email"
            rules={[
              { required: true, message: "Preencha o email." },
              { type: "email", message: "Email inválido." },
            ]}
          >
            <Input placeholder="Email" />
          </Form.Item>
        </Form>
      </Modal>
    </div>
  );
};

export default withRouter(Home);
