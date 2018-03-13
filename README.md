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

    $pdo = 'Sua conexão com o banco de dados';
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

No modo 2 e 3 você pode checar se a conexão com o banco de dados foi iniciada, apenas chame `$crud->getLog()`. Você deverá receber algo como:

    Conexão com o banco de dados:
    Inicializada | ::1 | 11-02-2018 21:34:14

## Métodos CRUD
Agora que sabemos como iniciar a conexão com o banco de dados vamos aprender a utilizar os métodos CRUD.

### Criando registros
A criação de registros com essa classe é bastante fácil, basta utilizar `$crud->setInserir()`.

    $crud->setInserir('usuario', array('nome' => 'Jeconias', 'email' => 'jeconiass2009@hotmail.com', 'senha' => 'senha'));

- Primeiro parâmetro: Insira o nome da sua tabela;
- Segundo parâmetro:
    - Insira uma array com as chaves iguais a coluna da sua tabela
        ex:
`array('nome' => 'Jeconias', 'email' => 'jeconiass2009@hotmail.com', 'senha' => 'senha');`

    - Uma array multidimensional.

    ex:
```
<?php
$arr = array(
            0 => array(
                'nome' => 'José',
                'data' => '2018-03-22'),
            1 => array(
                'nome' => 'Jeconias',
                'data' => '2018-03-25')
          );
```

- Terceiro parâmetro: Você pode criptografar o valor passando o nome da chave que corresponde ao valor desejado. Padrão: senha;

Se você deseja saber quantas linhas foram inseridas, use `$crud->getInserir`.

### Selecionando registros

    $crud->setSelect('usuario', array('nome', 'email'), array('email' => 'jeconiass2009@hotmail.com'), null, array('nome' => 'ASC'));

- Primeiro parâmetro: Nome da tabela;
- Segundo parâmetro: Uma array com os valores que deseja receber. Use `array(*)` para tudo;
- Terceiro parâmetro: Uma array que faz papel de **where**, ou seja, no exemplo a cima eu irei receber todos os registros que tenha o email correspondente. Você pode utilizar mais de um valor, ex: `array('nome' => Jeconias, 'Cidade' => 'Natal')`;
- Quarto parâmetro: Utilize um valor inteiro para definir o limite de registros selecionados;
- Quinto parâmetro: Uma array para classificar o conjunto de resultados em ordem ascendente ou decrescente. Use **DESC** ou **ASC**.

#### Monte sua SQL

Se a Classe não suporta (ainda e.e) sua SQL, você pode montar uma para não abandonar ou ter que usar duas classes para CRUD.

    $crud->setSelectsql('SELECT * FROM registrodehoras WHERE data BETWEEN :inicio AND :fim', array('inicio' => '2018-02-01', 'fim' => '2018-03-05'));

Você deve utilizar `$crud->getSelect()` para obter os últimos valores retornados pelo método `$crud->setSelect` ou `$crud->setSelectsql`

### Atualizando registros

    $crud->setUpdate('usuario', array('email' => 'exemplo@hotmail.com', 'nome' => 'exemplo'), array('id' => 5));

- Primeiro parâmetro: Nome da tabela;
- Segundo parâmetro: Uma array com os valores que deseja atualizar.
- Terceiro parâmetro: Defina qual registro irá atualizar. **where**.

Utilize `$crud->getUpdate` para receber a quantidade de linhas afetadas pelo método `$crud->setUpdate`.

### Deletando registros

    $crud->setDelete('usuario', array('id' => 3));

- Primeiro parâmetro: Nome da tabela;
- Segundo parâmetro: Uma array com os valores que deseja remover.
- Obs: Se você não passar o segundo parâmetro, **TUDO** será apagado da tabela.

Use `$crud->getDelete` para obter o número de linhas afetadas pelo método `$crud->setDelete()`.

### Log

Use `$crud->getLog()` para receber o último logo gerado.

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

## Sobre
- Autor: Jeconias Santos
- Licença: [MIT License](https://opensource.org/licenses/MIT)
- Copyright: Jeconias Santos
- Status: Desenvolvimento
