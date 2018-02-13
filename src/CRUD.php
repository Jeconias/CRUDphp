<?php
/**
 * @author     Jeconias Santos <jeconiass2009@hotmail.com>
 * @license    https://opensource.org/licenses/MIT MIT License
 * @copyright  Jeconias Santos
 * @version    v1.0.0
 *  Você pode utilizar essa class como quiser, contando que mantenha os créditos
 *  originais em todas as cópias!
 *
 *  Ainda irei finalizar os comentários!
 */
namespace PlusCrud\Crud;

class Crud
{
    private $conexao     = null; // CONEXÃO DO BANCO DE DADOS
    private $DBHost      = null; // HOST PARA CONEXÃO DO BANCO DE DADOS
    private $DBName      = null; // NOME DO BANCO DE DADOS
    private $DBUser      = null; // USUÁRIO DO BANCO DE DADOS
    private $DBPass      = null; // SENHA DO BANCO DE DADOS
    private $log         = null; // LOG PARA VISUALIZAR QUANDO FOR NECESSÁRIO

    private $Inserido    = null; // QUANTIDADE DE REGISTROS INSERIDOS NA ÚLTIMA QUERY
    private $Selecionado = null; // DADOS SELECIONADOS DA ÚLTIMA QUERY
    private $Atualizado  = null; // QUANTIDADE DE REGISTROS ATUALIZADOS NA ÚLTIMA QUERY
    private $Removido    = null; // QUANTIDADE DE REGISTROS DELETADOS NA ÚLTIMA QUERY

    public function __construct($conexao = null, $config = null)
    {
        if ($conexao !== null && $config == null) {
            $this->conexao = $conexao;
        } elseif ($conexao == null && $config !== null) {
            $this->pdo($config[0], $config[1], $config[2], $config[3]);
        }
    }

    public function setDBHost($v)
    {
        $this->DBHost = $v;
    }
    public function setDBName($v)
    {
        $this->DBName = $v;
    }
    public function setDBUser($v)
    {
        $this->DBUser = $v;
    }
    public function setDBPass($v)
    {
        $this->DBPass = $v;
    }

    public function setInserir($tabela, $valores, $senha = 'senha')
    {
        if (!is_array($valores)) {
            $this->log .= '<b>Inserindo dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>setInserir</b> não é uma array<br>';
            $this->Inserido = false;
            return false;
        }
        $this->inserir($tabela, $valores, $senha);
    }
    public function setSelect($tabela, $valores, $where = null, $limit = null, $order = null)
    {
        if (!is_array($valores)) {
            $this->log .= '<b>Selecionado dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>setSelect</b> não é uma array<br>';
            $this->Selecionado = false;
            return false;
        } elseif ($where !== null && !is_array($where)) {
            $this->log .= '<b>Selecionado dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$where</b> do método <b>setSelect</b> não é uma array<br>';
            $this->Selecionado = false;
            return false;
        } elseif ($order !== null && !is_array($order)) {
            $this->log .= '<b>Selecionado dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$order</b> do método <b>setSelect</b> não é uma array<br>';
            $this->Selecionado = false;
            return false;
        }

        $this->select($tabela, $valores, $where, $limit, $order);
    }
    public function setUpdate($tabela, $valores, $where, $senha = 'senha')
    {
        if (!is_array($valores)) {
            $this->log .= '<b>Atualizando dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>setUpdate</b> não é uma array<br>';
            $this->Atualizado = false;
            return false;
        } elseif ($where !== null && !is_array($where)) {
            $this->log .= '<b>Atualizando dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$where</b> do método <b>setUpdate</b> não é uma array<br>';
            $this->Atualizado = false;
            return false;
        }

        $this->update($tabela, $valores, $where, $senha);
    }
    public function setDelete($tabela, $where = null)
    {
        if ($where !== null && !is_array($where)) {
            $this->log .= '<b>Deletando dados:</b><br>';
            $this->log .= 'Erro: A variável <b>$where</b> do método <b>setDelete</b> não é uma array<br>';
            $this->Removido = false;
            return false;
        }

        $this->Delete($tabela, $where);
    }

    public function getInserir()
    {
        return $this->Inserido;
    }
    public function getSelect()
    {
        return $this->Selecionado;
    }
    public function getUpdate()
    {
        return $this->Atualizado;
    }
    public function getDelete()
    {
        return $this->Removido;
    }

    public function getLog()
    {
        return $this->log;
    }

    public function run()
    {
        $this->pdo($this->DBHost, $this->DBName, $this->DBUser, $this->DBPass);
    }

    private function pdo($host, $dbname, $dbuser, $dbpass)
    {
        try {
            $pdo = new \PDO('mysql:host='.$host.'; dbname='.$dbname, $dbuser, $dbpass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conexao = $pdo;
            $this->log .= '<br><b>Conexão com o banco de dados:</b><br>';
            $this->log .= 'Inicializada | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
        } catch (\PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    private function inserir($tabela, $fields, $senha)
    {
        try {
            foreach ($fields as $key => $value) {
                $keys [] = ':'.$key;
            }

            $first_keys = implode(', ', array_keys($fields));
            $last_keys = implode(', ', array_values($keys));

            $sql = 'INSERT INTO '.$tabela.' ('.$first_keys.') VALUES ('.$last_keys.')';
            $query = $this->conexao->prepare($sql);

            foreach ($fields as $key => $value) {
                if ($key == $senha) {
                    $query->bindvalue(':'.$key, $this->hash($value));
                } else {
                    $query->bindvalue(':'.$key, $value);
                }
            }
            $query->execute();
            $this->log .= '<b>Inserindo dados:</b><br>';
            $this->log .= 'Sucesso | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            $this->Inserido = $query->rowCount();
            return true;
        } catch (\Exception $e) {
            $this->log .= '<b>Inserindo dados:</b><br>';
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            return false;
        }
    }

    private function select($tabela, $valores, $where, $limit, $orderBy)
    {
        try {
            if ($where != null) {
                $arr_where = $where;
                $count = count($where);
                $where = array_keys($where);
                $where_sql = 'WHERE '.$where[0].'=:'.$where[0];

                $a = 1;
                while ($a < $count) {
                    if (!ctype_alpha($where[$a])) {
                        return false;
                    }
                    if (count($where) > 1) {
                        $where_sql .= ' AND '.$where[$a].'=:'.$where[$a];
                    }
                    $a++;
                }
            } else {
                $where_sql = null;
            }

            if ($limit != null && is_numeric($limit)) {
                $limit = 'LIMIT '.$limit;
            } else {
                $limit = null;
            }

            if (count($valores) >= 1 && is_array($valores)) {
                $valores = implode(', ', $valores);
            }

            if ($orderBy != null && count($orderBy) == 1) {
                $filter = array_keys($orderBy);
                $order = array_values($orderBy);
                $orderBy = 'ORDER BY '.$filter[0].' '.$order[0];
            } else {
                $orderBy = null;
            }

            $sql = 'SELECT '.$valores.' FROM '.$tabela.' '.$where_sql.' '.$orderBy.' '.$limit;
            $query = $this->conexao->prepare($sql);

            if ($where != null) {
                foreach ($arr_where as $key => $value) {
                    $query->bindValue(':'.$key, $value);
                }
            }
            $query->execute();
            $this->log .= '<b>Selecionando dados:</b><br>';
            $this->log .= 'Sucesso | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            $this->Selecionado = $query->fetchAll(\PDO::FETCH_ASSOC);
            return true;
        } catch (\Exception $e) {
            $this->log .= '<b>Selecionando dados:</b><br>';
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            return false;
        }
    }



    private function update($tabela, $valores, $where, $senha)
    {
        try {
            $key = array_keys($valores);
            foreach ($key as $value) {
                $keys [] = $value.'=:'.$value;
            }
            $keys = implode(', ', $keys);
            $arr_where = $where;
            $count = count($where);
            $where = array_keys($where);
            $where_sql = 'WHERE '.$where[0].'=:'.$where[0];

            $a = 1;
            while ($a < $count) {
                if (!ctype_alpha($where[$a])) {
                    return false;
                }
                if (count($where) > 1) {
                    $where_sql .= ' AND '.$where[$a].'=:'.$where[$a];
                }
                $a++;
            }

            $sql = 'UPDATE '.$tabela.' SET '.$keys.' '.$where_sql;
            $query = $this->conexao->prepare($sql);
            foreach ($valores as $key => $value) {
                if ($key == $senha) {
                    $query->bindvalue(':'.$key, $this->setHash($value));
                } else {
                    $query->bindvalue(':'.$key, $value);
                }
            }
            foreach ($arr_where as $key => $value) {
                $query->bindvalue(':'.$key, $value);
            }
            $query->execute();
            $this->log .= '<b>Inserindo dados:</b><br>';
            $this->log .= 'Sucesso | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            $this->Atualizado = $query->rowCount();
            return true;
        } catch (\Exception $e) {
            $this->log .= '<b>Atualizando dados:</b><br>';
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            return false;
        }
    }

    private function Delete($tabela, $where)
    {
        try {
            if ($where != null) {
                $arr_where = $where;
                $count = count($where);
                $where = array_keys($where);
                $where_sql = ' WHERE '.$where[0].'=:'.$where[0];

                $a = 1;
                while ($a < $count) {
                    if (count($where) > 1) {
                        $where_sql .= ' AND '.$where[$a].'=:'.$where[$a];
                    }
                    $a++;
                }
            } else {
                $where_sql = null;
            }

            $sql = 'DELETE FROM '.$tabela.$where_sql;
            $query = $this->conexao->prepare($sql);

            if ($where_sql !== null) {
                foreach ($arr_where as $key => $value) {
                    $query->bindvalue(':'.$key, $value);
                }
            }

            $query->execute();
            $this->log .= '<b>Inserindo dados:</b><br>';
            $this->log .= 'Sucesso | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            $this->Removido = $query->rowCount();
            return true;
        } catch (\Exception $e) {
            $this->log .= '<b>Removendo dados:</b><br>';
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').'<br>';
            return false;
        }
    }

    //CRIPTOGRAFIA DE SENHA POR HASH
    private function salt()
    {
        $string = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
        $retorno = '';
        for ($i = 1; $i <= 22; $i++) {
            $rand = mt_rand(1, strlen($string));
            $retorno .= $string[$rand-1];
        }
        return $retorno;
    }

    private function hash($senha)
    {
        return crypt($senha, '$2a$10$' . $this->salt() . '$');
    }
}
