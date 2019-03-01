<?php

defined('BASEPATH') OR exit("No access script");

class Data_Model extends CI_Model
{
    protected $meses = array('', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

    public function __construct()
    {
        parent::__construct();
    }

    public function Get($table)
    {
        try 
        {
            return $this->db->get($table)->result();
        } 
        catch (\Exception $ex) { throw $ex; }

    }

    public function GetWhere($table, $id)
    {
        try 
        {
            return $this->db->where("id_{$table}", $id)->get($table)->row();
        } 
        catch (\Exception $ex) { throw $ex; }

    }

    public function Save($table, $data)
    {
        try 
        {
            $this->db->insert($table, $data);

            return $this->db->insert_id();
        } 
        catch (\Exception $ex) { throw $ex; }
    }

    public function Delete($table, $id)
    {
        try 
        {
            return $this->db->delete($table, "id_{$table} = $id");
        } 
        catch (\Exception $ex) { throw $ex; }
    }

    public function Update($table, $id, $data)
    {
        try 
        {
            return $this->db->update($table, $data, "id_{$table} = $id");
        } 
        catch (\Exception $ex) { throw $ex; }
    }


    public function GetConsumo($pagos = FALSE, int $mes = 0, int $ano = 0)
    {
        try 
        {
            $this->db
                ->select("consumo.id_consumo, usuario.id_usuario, usuario.nome, cafe.id_cafe, cafe.tipo, cafe.valor, consumo.data_hora, CASE WHEN consumo.pago IS TRUE THEN 'Pago' ELSE 'Devendo' END as pago")
                ->from("consumo")
                ->join("usuario", "id_usuario", "inner")
                ->join("cafe", "id_cafe", "inner")
                ->where("id_usuario", $this->session->userdata('id'));

            if (!is_null($pagos))
                $this->db->where("consumo.pago", $pagos);

            if ($mes != 0 && $ano != 0)
                $this->db->where("DATE_PART('month', consumo.data_hora) = {$mes}")->where("DATE_PART('year', consumo.data_hora) = {$ano}");

            return 
                $this->db
                    ->order_by('consumo.data_hora', 'desc')
                    ->get()
                    ->result();
        } 
        catch (\Exception $ex) { throw $ex; }
    }

    public function GetValorConsumo()
    {
        try 
        {
            $consumos = $this->GetConsumo();

            $valor_total = 0;

            foreach ($consumos as $consumo):
                
                $valor_total += $consumo->valor;

            endforeach;

            return $valor_total;
        } 
        catch (\Exception $ex) { throw $ex; }
    }

    public function GetMesesConsumo()
    {
        try
        {
            $meses_consumo = $this->db 
                ->select("DATE_PART('month', consumo.data_hora) as mes, DATE_PART('year', consumo.data_hora) as ano")
                ->from("consumo")
                ->where("id_usuario", $this->session->userdata('id'))
                ->group_by("mes, ano")
                ->order_by("ano", "desc")
                ->order_by("mes", "desc")
                ->get()
                ->result();

            foreach ($meses_consumo as $data):
                
                $data->mes_extenso = $this->meses[$data->mes];

            endforeach;

            return $meses_consumo;
        } 
        catch (\Exception $ex) { throw $ex; }
    }

    public function PagarConsumo(int $id)
    {
        try
        {
            $where = "id_" . Tables::CONSUMO . "= {$id}";

            $this->db->update(Tables::CONSUMO, ['pago' => true], $where);
        } 
        catch (\Exception $ex) { throw $ex; }
    }
}
