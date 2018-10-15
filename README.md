# Classe CRUD

Se você quer reduzir os números de linhas e evitar muitas querys e conexões com o banco de dados você pode utilizar essa classe CRUD para facilitar sua vida.

   - Fácil de usar
   - Mini sistema de log
   - Reaproveitar conexão com o Banco de dados

Obtenha a classe:

    composer require pluscrud/crud

# Conhecendo a Classe
Aqui irei descrever todo o funcionamento da classe e como utilizar todos os métodos que contém disponíveis no momento.

### Como eu posso inicializar a classe?
Você pode iniciar a classe de 3 maneiras distintas, quem decide é você! ;)

#### Modo 1:
Você pode utilizar sua classe de conexão com o banco de dados e passar a conexão atual para o CRUD.

    $pdo = 'Seu objeto de conexão com o banco de dados';
    $crud = new \PlusCrud\Crud\CRUD($pdo);

#### Modo 2:
Você pode utilizar **define** ou array.

    define('CONFIG', ['localhost', 'teste', 'root', 'senha' ]);
    $crud = new \PlusCrud\Crud\CRUD(null, CONFIG);
    ------------------------------------------------------------------------------
    $config = array('localhost', 'teste', 'root', 'senha');
    $crud = new \PlusCrud\Crud\CRUD(null, $config);

#### Modo 3:
Se você não quiser utilizar **define** ou **array**, utilize os métodos internos do CRUD.  

    $crud = new \PlusCrud\Crud\CRUD();
    $crud->setDBHost('localhost');
    $crud->setDBName('teste');
    $crud->setDBUser('root');
    $crud->setDBPass('senha');
    $crud->run();

- Se você utilizar o modo 3 você deve chamar o `$crud->run()` para carregar a conexão com o banco de dados.

No modo 2 e 3 você pode checar se a conexão com o banco de dados foi iniciada, apenas chame `$crud->log()`. Você deverá receber algo como:

    Conexão com o banco de dados:
    Inicializada | ::1 | 11-02-2018 21:34:14

## Métodos CRUD
Agora que sabemos como iniciar a conexão com o banco de dados vamos aprender a utilizar os métodos.

### Criando registros
A criação de registros com essa classe é bastante fácil, basta utilizar `$crud->insert()`.

    $crud->insert('usuario', array('nome' => 'Jeconias', 'email' => 'jeconiass2009@hotmail.com', 'senha' => 'senha'));

- **Primeiro parâmetro:** Insira o nome da sua tabela;
- **Segundo parâmetro:**
Insira uma array com as chaves iguais a coluna da sua tabela ou uma array multidimensional.
**Exemplo array simples:**

        array('nome' => 'Jeconias', 'email' => 'jeconiass2009@hotmail.com', 'senha' => 'senha');

    **Exemplo array multidimensional:**

        $arr = array(
            0 => array(
                'nome' => 'José',
                'data' => '2018-03-22'),
            1 => array(
                'nome' => 'Jeconias',
                'data' => '2018-03-25')
         );


- **Terceiro parâmetro:** Você pode criptografar o valor passando o nome da chave que corresponde ao valor desejado. Padrão: senha;

    `$crud->inserir` retorna a quantidade de linha inseridas.

### Selecionando registros

    $crud->select('usuario', array('nome', 'email'), array('email' => 'jeconiass2009@hotmail.com'), null, array('nome' => 'ASC'));

- **Primeiro parâmetro:** Nome da tabela;
- **Segundo parâmetro:** Uma array com os valores que deseja receber. Use `array(*)` para tudo;
- **Terceiro parâmetro:** Uma array que faz papel de **where**, ou seja, no exemplo a cima eu irei receber todos os registros que tenha o email correspondente. Você pode utilizar mais de um valor, ex: `array('nome' => Jeconias, 'Cidade' => 'Natal')`;

    ###### **Versão >= 1.0.8**

    - Nas versões acima de 1.0.8 você pode definir o tipo de condição para cada valor.

    **Exemplo:**

        array('versao' => '2.2', 'joker' => array('>'))

    Você pode inserir mais "Jokers":

    **Outro exemplo:**

        array('versao' => '2.2' 'tipo' => 'stable', 'joker' => array('>', '='))


    **PS.:**

    - Mantenha a sequência das condições iguais as das seleções;
    - Sempre utiliza o nome "joker" para setar as condições;
    - O valor padrão para WHERE é "=" se você não utilizar o joker.


- **Quarto parâmetro:** Utilize um valor inteiro para definir o limite de registros selecionados;
- **Quinto parâmetro:** Uma array para classificar o conjunto de resultados em ordem ascendente ou decrescente. Use **DESC** ou **ASC**.

    #### Monte sua SQL

    Se a Classe não suporta (ainda e.e) sua SQL, você pode montar uma para não abandonar ou ter que usar duas classes para CRUD.

        $crud->selectManual('SELECT * FROM registrodehoras WHERE data BETWEEN :inicio AND :fim', array('inicio' => '2018-02-01', 'fim' => '2018-03-05'));

    `$crud->select()` e `$crud->selectManual` retornam os valores selecionados.

### Atualizando registros

    $crud->update('usuario', array('email' => 'exemplo@hotmail.com', 'nome' => 'exemplo'), array('id' => 5));

- **Primeiro parâmetro:** Nome da tabela;
- **Segundo parâmetro:** Uma array com os valores que deseja atualizar.
- **Terceiro parâmetro:** Defina qual registro irá atualizar. **where**.

`$crud->update` retorna a quantidade de linhas afetadas.

### Deletando registros

    $crud->delete('usuario', array('id' => 3));

- **Primeiro parâmetro:** Nome da tabela;
- **Segundo parâmetro:** Uma array com os valores que deseja remover.
- **Obs:** Se você **NÃO** passar o segundo parâmetro **OU** passar `array('*')` , **TUDO** será apagado da tabela.

`$crud->delete` retorna o número de linhas removidas.

### Inserir tradução do log

Você pode utilizar um arquivo PHP externo para alterar o idioma padrão dos logs. O arquivo deve ter o seguinte nome:
**class.pluscrud.lang.SIGLA_DO_IDIOMA.php**.
**Exemplo:** class.pluscrud.lang.en.php

Basta chamar o método `$crud->setLanguage` e utilizar dessa forma:

    $crud->setLanguage('en', 'Diretorio do arquivo');

Se você deseja fazer uma tradução, basta utilizar o arquivo padrão como base. Ele está localizado [aqui](https://github.com/jeconiassantos/crud/tree/master/src).

### Log

Use `$crud->log()` para receber as ações e erros gerados.

## Todos os métodos

| Método | Ação | Retorno |
|--------|------|---------|
|setLanguage() | Alterar idioma dos registros | boolean |
|setDBHost() | Inserir nome do host/servidor |  nothing |
|setDBName() | Nome do banco de dados. | nothing |
|setDBUser() | Nome do usuário do banco de dados. | nothing |
|setDBPass() | Senha do banco de dados. | nothing |
|run() | Iniciar a conexão do banco de dados (necessário apenas quando utilizar o modo 3 de conexão com o DB). | nothing |
|insert() | Inserir dados | A quantidade de linhas inseridas |
|select() | Selecionar dados | Todos os dados selecionados |
|selectManual() | Selecionar dados com SQL manual | Os dados selecionados |
|update() | Atualizar dados | Quantidade de linhas alteradas |
|delete() | Remover linhas | A quantida de linhas afetadas |
|log() | nothing | Todo o log do objeto |

## Changelog

- 1.0.1

Corrigido problema na instância do CRUD e PDO.

- 1.0.2

Adicionado mais comentários na classe.

- 1.0.3

Correção do método hash.

- 1.0.4

Suporte para múltiplos "inserts".

- 1.0.5

Suporte para SQL montada.

- 1.0.6

Comentários e pequenas correções;

Melhorado o sistema de logs.

- 1.0.7

Correção para múltiplos inserts.

- 1.0.8

Agora é possível utilizar WHERE com outras condições (>, <>, <, etc...);
Corrigido o problema para inserir arrays multidimensional

- 1.0.9

Correção ao inserir várias linhas com arrays;

Suporte para receber traduções para os logs;

Alterado os nomes dos metodos para melhorar o uso;

A versão 1.0.9 **NÃO** é compatível com as anteriores.


## Sobre
- Licença: [MIT License](https://opensource.org/licenses/MIT)
- Copyright: Jeconias Santos
- Status: Desenvolvimento
