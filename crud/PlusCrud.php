<?php
/**
 * 
 * @package     PlusCrud
 * @author      Jeconias Santos <jeconiass2009@hotmail.com>
 * @license     https://opensource.org/licenses/MIT - MIT License
 * @see         https://github.com/jeconiassantos/CRUDphp Documentação
 * @copyright   Jeconias Santos
 * @since       v1.1.7
 * 
 *  Você pode utilizar essa class como quiser, contando que mantenha os créditos
 *  originais em todas as cópias!
 *
 *  Ainda irei finalizar os comentários!
 *  A tradução ainda não está completa
 */
namespace PlusCrud;

final class PlusCrud
{   

    /** 
     * 
     * @var PlusCrud $instance Instancia da class
     * 
     */
    private static $instance = null;


    /** 
     * 
     * @var PDO $conexao Objeto de conexão com o banco de dados (PDO)
     * 
     */
    private $conexao = null;


    /** 
     * 
     * @var string $DBHost Host para conexão do banco de dados
     * 
     */
    private $DBHost = null;


    /** 
     * 
     * @var string $DBName Nome do banco de dados
     * 
     */
    private $DBName = null;


    /** 
     * 
     * @var string $DBUser Usuário do banco de dados
     * 
     */
    private $DBUser = null;


    /** 
     * 
     * @var string $DBPass Senha para conexão do banco de dados
     * 
     */
    private $DBPass = null;


    /** 
     * 
     * @var string $log Registros dos processos de manipulação dos dados no banco
     * 
     */
    private $log = null;


    /** 
     * 
     * @var array $language Array com o idioma do Objeto atual
     * 
     */
    private $language = array();


    /** 
     * @param PDO|null $conexao do banco de dados ou null
     * @param array|null $config Configurações para conexão ou null
     * 
     * @return void
     */
    private function __construct(?PDO $conexao, ?array $config)
    {
        if ($conexao !== null && $config == null) {
            $this->conexao = $conexao;
        } elseif ($conexao == null && $config !== null) {
            $this->pdo($config[0], $config[1], $config[2], $config[3]);
        }
        $this->init();
    }


    /** 
     * @return void
     */
    private function init()
    {
      $this->language = array(
        'default' => 'pt_br',
        'translation' => array(
          'action_change_language' => 'Idioma alterado para',
          'action_dbhost' => 'Adicionado endereço do servidor do banco de dados;',
          'action_dbname' => 'Adicionado nome do banco de dados;',
          'action_dbuser' => 'Adicionado usuário do banco de dados;',
          'action_dbpass' => 'Adicionado senha do banco de dados;',
          'action_startdb' => 'Iniciando conexão com o banco de dados;',
          'action_connectiondb' => 'Conexão com o banco de dados',
          'action_connectiondb_init' => 'Inicializada',
          'warning_jokers' => 'Warning: Os Jokers não foram inseridos para a utilização do WHERE. Usando o padrão: "=";',
          'error_connection' => 'Erro: Conexão com o banco de dados não estabelecida;',
          'error_change_language' => 'Falha ao tentar alterar o idioma;',
        )
      );
    }


    /** 
     * @param PDO|null $conexao do banco de dados ou null
     * @param array|null $config Configurações para conexão ou null
     * 
     * @return void
     */
    public static function getInstance(?PDO $conexao = null, ?Array $config = null)
    {
        if(self::$instance === null) self::$instance = new PlusCrud($conexao, $config);
        return self::$instance;
    }

    
    /** 
     * @param string $v Hostname
     * 
     * @return PlusCrud
     */
    public function setDBHost(string $v)
    {
        $this->DBHost = $v;
        $this->log .= $this->language['translation']['action_dbhost'].'<br>';
        return self::$instance;
    }
    

    /** 
     * @param string $v Nome do banco de dados
     * 
     * @return PlusCrud
     */
    public function setDBName(string $v)
    {
        $this->DBName = $v;
        $this->log .= $this->language['translation']['action_dbname'].'<br>';
        return self::$instance;
    }


    /** 
     * @param string $v Nome do usuário do banco
     * 
     * @return PlusCrud
     */
    public function setDBUser(string $v)
    {
        $this->DBUser = $v;
        $this->log .= $this->language['translation']['action_dbuser'].'<br>';
        return self::$instance;
    }
    

    /** 
     * @param string $v Senha do usuário do banco
     * 
     * @return PlusCrud
     */
    public function setDBPass(string $v)
    {
        $this->DBPass = $v;
        $this->log .= $this->language['translation']['action_dbpass'].'<br>';
        return self::$instance;
    }


    /** 
     * @param string $lang Idioma referente a classe de tradução. Ex: pt-br, en e etc.
     * Onde "pt-br" é parte do nome do arquivo de tradução para ser utilizado. Ex: class.pluscrud.lang.pt-br
     * @param string $v Directório das traduções
     * 
     * @return bool
     */
    public function setLanguage(string $lang, string $directory)
    {
      return $this->changeLanguage($lang, $directory);
    }


    /** 
     * @param string $tabela Nome da tabela
     * @param array $valores Valores para ser inseridos. EX: array("tipo" => "admin).
     * "tipo" é o nome da coluna e "admin" o valor a ser inserido.
     * @param string $senha Valor do index do array. O valor referente ao index será criptografado.
     * 
     * @return int|null
     */
    public function insert(string $tabela, array $valores, string $senha = 'senha') : ?int
    {
        if (!is_array($valores)) {
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        }

        if ($valores == null) {
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>'.__FUNCTION__.'</b> é null;<br>';
            return null;
        }
        
        if ($this->conexao == null) {
          $this->log .= $this->language['translation']['error_connection'].'<br>';
          return null;
        }
        return $this->actionInsert($tabela, $valores, $senha);
    }

    
    /** 
     * @param string $tabela Nome da tabela
     * @param array $valores Valores para serem capturados (as colunas)
     * @param array $where Ex: array('email' => 'jeconias@olamundoweb.com.br') ou array('versao' => '2.2', 'joker' => array('>')).
     * Onde versão deve ser maior que 2.2.
     * @param int $limit 10
     * @param array $order array('nome' => 'ASC')
     * 
     * @return array|null
     */
    public function select(string $tabela, array $valores, ?array $where = null, ?int $limit = null, ?array $order = null) : ?array
    {
        if (!is_array($valores)) {
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        }elseif ($where != null && !is_array($where)) {
            $this->log .= 'Erro: A variável <b>$where</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        }elseif ($order != null && !is_array($order)) {
            $this->log .= 'Erro: A variável <b>$order</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        }elseif ($where != null && !isset($where['joker'])) {
            $this->log .= $this->language['translation']['warning_jokers'].'<br>';
        }elseif ($valores == null){
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>'.__FUNCTION__.'</b> está null;<br>';
            return null;
        }

        if ($this->conexao == null) {
          $this->log .= $this->language['translation']['error_connection'].'<br>';
          return null;
        }

        return $this->actionSelect($tabela, $valores, $where, $limit, $order);
    }

    /** 
     * @param string $tabela Nome da tabela. EX: SELECT * FROM registrodehoras WHERE data BETWEEN :inicio AND :fim
     * @param array $valores Ex: array('inicio' => '2018-02-01', 'fim' => '2018-03-05')
     * 
     * @return array|null
     */
    public function selectManual(string $sql, ?array $valores = null) : ?array
    {
        if ($this->conexao == null) {
          $this->log .= $this->language['translation']['error_connection'].'<br>';
          return null;
        }
        return $this->selectSql($sql, $valores);
    }

    
    /** 
     * @param string $tabela Nome da tabela.
     * @param array $valores
     * @param array $where
     * @param string $senha
     * 
     * @return int|null
     */
    public function update(string $tabela, array $valores, array $where, ?string $senha = 'senha') : ?int
    {
        if (!is_array($valores)) {
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        } elseif ($where !== null && !is_array($where)) {
            $this->log .= 'Erro: A variável <b>$where</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        }elseif($valores == null){
            $this->log .= 'Erro: A variável <b>$valores</b> do método <b>'.__FUNCTION__.'</b> está null;<br>';
            return null;
        }

        if ($this->conexao == null) {
          $this->log .= $this->language['translation']['error_connection'].'<br>';
          return null;
        }

        return $this->actionUpdate($tabela, $valores, $where, $senha);
    }

    
    /** 
     * @param string $tabela Nome da tabela.
     * @param array $where
     * 
     * @return int|null
     */
    public function delete(string $tabela, ?array $where = null) : ?int
    {
        if ($where !== null && !is_array($where)) {
            $this->log .= 'Erro: A variável <b>$where</b> do método <b>'.__FUNCTION__.'</b> não é uma array;<br>';
            return null;
        }

        if ($this->conexao == null) {
          $this->log .= $this->language['translation']['error_connection'].'<br>';
          return null;
        }

        return $this->actionDelete($tabela, $where);
    }

    //RETORNA O LOG GERADO DURANTE A UTILIZAÇÃO DA INSTÂNCIA DA CLASS
    public function log() : string
    {
        return $this->log;
    }

    /*INICIA UMA CONEXÃO COM O BANCO DE DADOS QUANDO O USUÁRIO PASSA OS
     * VALORES PELOS MÉTODOS ESPECIFICOS. */
    public function run()
    {
        $this->log .= $this->language['translation']['action_startdb'].'<br>';
        $this->pdo($this->DBHost, $this->DBName, $this->DBUser, $this->DBPass);
    }


    /**
     * VERIFICAR SE A CONEXÃO COM O BANCO DE DADOS EXISTE  
     * @return bool
     */
    public function connStatus() : bool
    {
        if($this->conexao instanceof PDO) return true;
        return false;
    }

    //INICIAR UMA CONEXÃO COM O BANCO DE DADOS
    private function pdo($host, $dbname, $dbuser, $dbpass)
    {
        try {
            $pdo = new \PDO('mysql:host='.$host.'; dbname='.$dbname, $dbuser, $dbpass, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 

utf8"));
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conexao = $pdo;
            $this->log .= '<b>'.$this->language['translation']['action_connectiondb'].' { </b><br>';
            $this->log .= $this->language['translation']['action_connectiondb_init'] . ' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y 

H:i:s').';';
            $this->log .= '<b>}</b><br>';
            return true;
        } catch (\PDOException $e) {
            $trace = $e->getTrace()[0];
            $this->log = '<b>Error:</b> ' . $e->getMessage() . '<br><b>File:</b> ' . $trace['file'] . '<br> <b>Line:</b> ' . $trace['line'];
            $this->conexao = null;
            return false;
        }
    }
    

    /** 
     * @param string $tabela Nome da tabela
     * @param array $valores Valores para ser inseridos. EX: array("tipo" => "admin).
     * "tipo" é o nome da coluna e "admin" o valor a ser inserido.
     * @param string $senha Valor do index do array. O valor referente ao index será criptografado.
     * 
     * @return int|null
     */
    private function actionInsert(string $tabela, array $fields, string $senha) : ?int
    {
        try {
            if (array_sum(array_map('is_array', $fields)) != 0) {
                //TOTAL DE CHAVES DE UMA ARRAY
                $keys_count = count($fields[0]);
                //NÚMERO DE ARRAYS VEZES O TOTAL DE CHAVES
                $total_count = count($fields) * $keys_count;
                //OS NOMES DAS CHAVES
                $chaves = implode(', ', array_keys($fields[0]));

                //ESSE WHILE GERA O SQL DE ACORDO COM $total_count, OU SEJA, SE O $total_count FOR IGUAL A 10
                // O WHILE VAI GERAR ALGO ASSIM: (?, ?, ?, ?, ?), (?, ?, ?, ?, ?)
                $i = 1;
                $controle = 1;
                $SQL_Generator = '(?';
                while ($i < $total_count) {
                    if (($controle * $keys_count) == $i) {
                        $SQL_Generator .= '), (';
                        $SQL_Generator .= '?';
                        $controle++;
                    } else {
                        $SQL_Generator .= ', ?';
                    }
                    $i++;
                }
                $SQL_Generator .= ')';
                $SQL = 'INSERT INTO '.$tabela.' ('.$chaves.') VALUES '.$SQL_Generator;
                $query = $this->conexao->prepare($SQL);

                $count = 1;
                array_walk_recursive($fields, function ($value, $key) use (&$count, &$query, &$senha) {
                    if ($key == $senha) {
                        $query->bindvalue($count, $this->hash($value));
                    } else {
                        $query->bindValue($count, $value);
                    }
                    $count++;
                });
                $query->execute();
            } else {
                foreach ($fields as $key => $value) {
                    $first_keys [] = ':'.$key;
                }

                $keys = implode(', ', array_keys($fields));
                $values = implode(', ', array_values($first_keys));

                $sql = 'INSERT INTO '.$tabela.' ('.$keys.') VALUES ('.$values.')';

                $query = $this->conexao->prepare($sql);

                foreach ($fields as $key => $value) {
                    if ($key == $senha) {
                        $query->bindvalue(':'.$key, $this->hash($value));
                    } else {
                        $query->bindvalue(':'.$key, $value);
                    }
                }
                $query->execute();
            }
            $this->log .= 'Dados Inseridos | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return $query->rowCount();
        } catch (\Exception $e) {
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return null;
        }
    }

    //SELECIONAR REGISTROS
    private function actionSelect(string $tabela, array $valores, ?array $where, ?int $limit, ?array $orderBy)
    {
        try {
            if ($where != null) {
                $arr_where = $where;
                $count = count($where);
                $where = array_keys($where);
                $joker = isset($arr_where['joker'][0])?$arr_where['joker'][0]:'=';
                $where_sql = 'WHERE '.$where[0].$joker.':'.$where[0];

                $a = 1;
                while ($a < $count) {
                    if (!ctype_alpha($where[$a])) {
                        return null;
                    }
                    if ($count > 1 && $where[$a] != 'joker') {
                        $joker = isset($arr_where['joker'][$a])?$arr_where['joker'][$a]:'=';
                        $where_sql .= ' AND '.$where[$a].$joker.':'.$where[$a];
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
                if (isset($arr_where['joker'])){
                  unset($arr_where['joker']);
                }
                foreach ($arr_where as $key => $value) {
                    $query->bindValue(':'.$key, $value);
                }
            }
            $query->execute();
            $this->log .= 'Dados Selecionados | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return null;
        }
    }

    //SELECIONAR REGISTROS COM SQL MONTADA
    private function selectSql(string $sql, ?array $valores) : ?array
    {
        try {

            $query = $this->conexao->prepare($sql);

            if ($valores != null) {
              foreach ($valores as $key => $value) {
                  $query->bindvalue(':'.$key, $value);
              }
            }
            $query->execute();
            $this->log .= 'Dados Selecionados | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return null;
        }
    }

    //ATUALIZAR REGISTROS
    private function actionUpdate(string $tabela, array $valores, array $where, ?string $senha)
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
                    return null;
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
                    $query->bindvalue(':'.$key, $this->hash($value));
                } else {
                    $query->bindvalue(':'.$key, $value);
                }
            }
            foreach ($arr_where as $key => $value) {
                $query->bindvalue(':'.$key, $value);
            }
            $query->execute();
            $this->log .= 'Dados Atualizados | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return $query->rowCount();
        } catch (\Exception $e) {
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return null;
        }
    }
    //REMOVER REGISTROS
    private function actionDelete(string $tabela, array $where) : ?int
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
            $this->log .= 'Dados Removidos | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return $query->rowCount();
        } catch (\Exception $e) {
            $this->log .= 'Erro: '.$e->getMessage().' | '.$_SERVER['REMOTE_ADDR'].' | '.date('d-m-Y H:i:s').';<br>';
            return null;
        }
    }

    private function changeLanguage(string $lang, string $directory) : bool
    {
      $file = $directory . '/class.pluscrud.lang.'.$lang.'.php';

      if(file_exists($file) && $this->language['default'] != $lang){
        include $file;

        if(isset($newTranslate)){
            $this->language['translation'] = array();
            $this->language['translation'] = array_merge($this->language['translation'], $newTranslate);

            if($this->language['translation'] != null){
              $this->language['default'] = $lang;
              $this->log .= $this->language['translation']['action_change_language'].' <b>'.$lang.'</b>; <br>';
              return true;
            }
            $this->init();
            $this->log .= $this->language['translation']['error_change_language'].'<br>';
            return false;
        }else{
          $this->log .= 'A variável <b>$newTranslate</b> não foi definida no arquivo de tradução; <br>';
          return false;
        }
      }else{
        $this->log .= 'O arquivo de tradução não existe: <b>'.$file.'</b>; <br>';
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

    private function hash(string $senha)
    {
        return crypt($senha, '$2a$10$' . $this->salt() . '$');
    }
}