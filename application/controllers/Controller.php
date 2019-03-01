<?php

defined('BASEPATH') OR exit("No access script");

class Controller extends CI_Controller
{
    protected $control = NULL;

    protected $meses = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

    public function __construct()
    {
        parent::__construct();

        $this->load->model("Data_Model", "data");

        $this->load->library("Tables");

        if ($this->uri->segment(1) == 'sair')
        {
            $this->session->sess_destroy();
            
            $this->input->set_cookie('access', NULL, -1);

            redirect(base_url());
        }

        $session = (object)$this->session->userdata();

        if (empty($session->loged) && $this->uri->segment(1) != 'login')
            redirect(base_url('login'));
        else
            $this->control = $this->session->userdata();        
    }

    public function login()
    {
        try 
        {
            $post = $this->input->post();

            $cookie = get_cookie("access");

            if (empty($post) && empty($cookie))
            {
                $this->parser->parse('include/header', array());
                $this->parser->parse('login/login', array());
                $this->parser->parse('include/footer', array());
            }
            else
            {
                if (isset($cookie))
                {
                    $value = explode(';', $this->encryption->decrypt($cookie));

                    if (sizeof($value) == 2)
                    {
                        $user = NULL;

                        foreach ($this->data->Get(Tables::USUARIO) as $res):
                            
                            if ($res->id_usuario == $value[0] && $res->acesso == $value[1])
                                $user = $res;

                        endforeach;

                        if (!is_null($user))
                        {
                            $this->session->set_userdata(
                                array(
                                    'id' => $user->id_usuario,
                                    'nome' => $user->nome,
                                    'loged' => TRUE
                                )
                            );

                            redirect(base_url());
                        }
                        else
                        {
                            $this->input->set_cookie('access', NULL, -1);

                            redirect(base_url('login'));
                        }
                    }
                }
                else
                {
                    if (isset($post['user']) && isset($post['pass']))
                    {
                        $user = NULL;

                        foreach ($this->data->Get(Tables::USUARIO) as $res):

                            if ($res->acesso == $post['user'] && $res->senha == md5($post['pass']))
                                $user = $res;

                        endforeach;

                        if (!is_null($user))
                        {
                            $this->session->set_userdata(
                                array(
                                    'id' => $user->id_usuario,
                                    'nome' => $user->nome,
                                    'loged' => TRUE
                                )
                            );

                            setcookie(
                                'access',
                                $this->encryption->encrypt(join(';', array($user->id_usuario, $user->acesso))),
                                strtotime("2030-02-10T17:41:04.000Z"),
                                "/",
                                '',
                                false,
                                false
                            );

                            redirect(base_url());
                        }
                        else
                        {
                            throw new Exception("Usuário não encontrado", 1);
                        }
                        
                    }
                    else
                    {
                        throw new Exception("Campo usuário e senha obrigatórios", 1);
                    }
                }
            }
        } 
        catch (\Exception $ex) 
        {
            $this->session->set_flashdata('error', $ex->getMessage());

            redirect(base_url('login'));
        }
    }

    public function index()
    {
        // Usuário conectado
        $usuario = $this->data->GetWhere(Tables::USUARIO, $this->session->userdata('id'));

        // Cafés cadastrados no sistema
        $cafes = $this->data->Get(Tables::CAFE);

        // Consumos do usuário
        $consumos = $this->data->GetConsumo();

        // Valor consumo mensal
        $valor_total_consumo = $this->data->GetValorConsumo();

        $disponivel = ($usuario->utilizar_limite ? ($usuario->limite - $valor_total_consumo) : FALSE);

        // Para cada café
        foreach ($cafes as $cafe):

            $cafe->consumo = 0;

            $cafe->disabled = ($usuario->utilizar_limite && $disponivel < $cafe->valor ? 'disabled' : '');
            
            $cafe->valor = 'R$ ' . number_format($cafe->valor, 2, ',', '.');

            foreach ($consumos as $consumo):

                if ($consumo->id_cafe == $cafe->id_cafe)
                    $cafe->consumo++;

            endforeach;

        endforeach;

        $data = array(
            'nome'  => $usuario->nome,
            'cafes' => $cafes,
            'mes'   => $this->meses[date('n')],
            'ano'   => date('Y'),
            'consumo_mensal' => 'R$ ' . number_format($valor_total_consumo, 2, ',', '.'),
            'utilizar_limite' => $usuario->utilizar_limite,
            'limite' => number_format($usuario->limite, 2, ',', '.')
        );
        
        if ($usuario->utilizar_limite):

            $data['consumo_mensal'] = number_format($valor_total_consumo, 2, ',', '.');
            $data['progress'] = (($valor_total_consumo * 100) / $usuario->limite);
            $data['left'] = ($data['progress'] > 1.25) ? ("{$data['progress']}% - " . strlen($data['consumo_mensal']) * 5) . "px" : ("1.25%");

        endif;

        $this->parser->parse('include/header', array());
        $this->parser->parse('include/navbar', array());
        $this->parser->parse('consumo/inicial', $data);
        $this->parser->parse('include/footer', array());
    }

    public function consumo(int $cafe)
    {
        try 
        {
            $id = $this->data->Save(
                Tables::CONSUMO, 
                (object) array(
                    'id_usuario' => $this->session->userdata('id'),
                    'id_cafe' => $cafe
                )
            );

            $this->session->set_flashdata('registro', array('msg' => 'Consumo registrado', 'id' => $id));
        }
        catch (\Exception $ex)
        {
            $this->session->set_flashdata('error', $ex->getMessage());
        }
        finally
        {
            redirect(base_url());
        }

    }

    public function desfazer_consumo(int $cafe)
    {
        try
        {
            $deleted = $this->data->Delete(Tables::CONSUMO, $cafe);

            if (!$deleted)
                throw new Exception("Não foi possível desfazer o registro", 1);
                
        }
        catch (\Exception $ex)
        {
            $this->session->set_flashdata('error', $ex->getMessage());
        }
        finally
        {
            redirect(base_url());
        }
        
    }

    public function consumo_detalhado()
    {
        $consumo_mensal = $this->data->GetConsumo();

        foreach ($consumo_mensal as $consumo):

            $consumo->data = date('d/m/Y H:i', strtotime($consumo->data_hora));
            $consumo->valor = 'R$ ' . number_format($consumo->valor, 2, ',', '.');

        endforeach;

        $data = array(
            'nome' => $this->session->userdata('nome'),
            'consumo' => $consumo_mensal,
            'consumo_mensal' => 'R$ ' . number_format($this->data->GetValorConsumo(), 2, ',', '.'),
            'mes' => $this->meses[date('n')],
            'ano' => date('Y'),
            'meses_consumo' => $this->data->GetMesesConsumo()
        );

        $this->parser->parse('include/header', array());
        $this->parser->parse('include/navbar', array());
        $this->parser->parse('consumo/detalhes', $data);
        $this->parser->parse('include/footer', array());
    }

    public function consumo_mensal()
    {
        $meses_consumo = $this->data->GetMesesConsumo();

        foreach ($meses_consumo as $mes_consumo):

            $mes_consumo->consumos = $this->data->GetConsumo(NULL, $mes_consumo->mes, $mes_consumo->ano);

            $mes_consumo->valor_total = 0;

            foreach ($mes_consumo->consumos as $consumo):

                $consumo->data = date('d/m/Y H:i', strtotime($consumo->data_hora));

                $mes_consumo->valor_total += $consumo->valor;

                $consumo->valor = 'R$ ' . number_format($consumo->valor, 2, ',', '.');

            endforeach;

            $mes_consumo->valor_total = number_format($mes_consumo->valor_total, 2, ',', '.');

        endforeach;

        array_map(
            function($mes_consumo){
                



                array_map(
                    function($consumo)
                    {
                        
                    },
                    $mes_consumo->consumos
                );
            },
            $meses_consumo
        );

        $data['meses_consumo'] = $meses_consumo;

        $this->parser->parse('include/header', array());
        $this->parser->parse('include/navbar', array());
        $this->parser->parse('consumo/consumo_mensal', $data);
        $this->parser->parse('include/footer', array());
    }

    public function configuracoes()
    {
        $this->parser->parse('include/header', array());
        $this->parser->parse('include/navbar', array());
        $this->parser->parse('consumo/configuracoes', array());
        $this->parser->parse('include/footer', array());
    }

    public function configuracoes_gasto()
    {
        $usuario = $this->data->GetWhere(Tables::USUARIO, $this->session->userdata('id'));

        $post = $this->input->post();

        if (!empty($post))
        {
            try 
            {
                if (isset($post['utilizar_limite']) && isset($post['limite']) && $post['limite'] <= 0)
                    throw new Exception("Limite deve ser maior do que zero.", 1);

                $this->data->Update(Tables::USUARIO, $usuario->id_usuario, array('utilizar_limite' => isset($post['utilizar_limite']), 'limite' => $post['limite']));

                $this->session->set_flashdata('success', 'Configuração salva com sucesso!');

                redirect(base_url());
            }
            catch (\Exception $ex)
            {
                $this->session->set_flashdata('error', $ex->getMessage());

                redirect(base_url('configuracoes/gasto'));   
            }
        }
        else
        {
            $data = array(
                'limite' => (float)number_format($usuario->limite, 2, ',', '.'),
                'utilizar_limite' => $usuario->utilizar_limite ? "checked" : ""
            );
    
            $this->parser->parse('include/header', array());
            $this->parser->parse('include/navbar', array());
            $this->parser->parse('consumo/configuracoes_gasto', $data);
            $this->parser->parse('include/footer', array());
        }
    }

    public function realizar_pagamento()
    {
        try
        {
            $post = explode(';', $this->input->post('id_pagamentos'));

            if (empty($post) || !is_array($post) || sizeof($post) == 0)
                throw new Exception("Não foi possível carregar os pagamentos", 1);

            $this->db->trans_begin();
            
            foreach ($post as $id_consumo):

                $this->data->PagarConsumo($id_consumo);

            endforeach;

            $this->db->trans_commit();
        }
        catch (Exception $ex)
        {
            $this->session->set_flashdata('error', $ex->getMessage());

            $this->db->trans_rollback();

            redirect(base_url('consumo/detalhado'));
        }
        finally
        {
            $this->session->set_flashdata('success', "Consumos pagos!");
            
            redirect(base_url());
        }
    }

}