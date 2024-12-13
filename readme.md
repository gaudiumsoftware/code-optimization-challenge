# Requisitos e Configuração para Rodar o Código

## Requisitos Necessários

### Ambiente de Desenvolvimento
- **PHP**: Versão 8.1.
- **Servidor Web**: NGINX configurado para interpretar arquivos PHP.
- **Banco de Dados**: MySQL.

### Banco de Dados
- Script SQL para criar as tabelas necessárias:
  ```sql
  CREATE DATABASE sistema_corridas;

  USE sistema_corridas;

  CREATE TABLE usuarios (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nome VARCHAR(255) NOT NULL,
      email VARCHAR(255) UNIQUE NOT NULL,
      senha VARCHAR(255) NOT NULL
  );

  CREATE TABLE motoristas (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nome VARCHAR(255) NOT NULL,
      email VARCHAR(255) UNIQUE NOT NULL,
      senha VARCHAR(255) NOT NULL,
      placa_veiculo VARCHAR(20) NOT NULL,
      modelo_veiculo VARCHAR(50) NOT NULL,
      status VARCHAR(20) NOT NULL DEFAULT 'disponível'
  );

  CREATE TABLE corridas (
      id INT AUTO_INCREMENT PRIMARY KEY,
      usuario_id INT NOT NULL,
      motorista_id INT DEFAULT NULL,
      origem VARCHAR(255) NOT NULL,
      destino VARCHAR(255) NOT NULL,
      status VARCHAR(50) NOT NULL DEFAULT 'pendente',
      FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
      FOREIGN KEY (motorista_id) REFERENCES motoristas(id)
  );
  ```

### Ferramentas Recomendadas
- **Postman** ou **cURL**: Para testar os endpoints da API.

---

## Passo a Passo para Configurar o NGINX

### 1. Instale o NGINX e o PHP-FPM

No Amazon Linux 2023/Fedora/Red Hat:
```bash
sudo yum update
sudo yum install nginx php8.1-fpm php8.1-mysqlnd
```

### 2. Configure o NGINX para Interpretar Arquivos PHP

Edite o arquivo de configuração do site (exemplo: `/etc/nginx/nginx.conf`):
```nginx
server {
    listen       80;
    listen       [::]:80;
    server_name  localhost;
    root         /var/www/html;
    index index.php;

    # Load configuration files for the default server block.
    include /etc/nginx/default.d/*.conf;
}
```

### 3. Reinicie o NGINX e o PHP-FPM
```bash
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### 4. Teste a Configuração
Crie um arquivo `info.php` em `/var/www/html/` com o seguinte conteúdo:
```php
<?php
phpinfo();
```
Acesse `http://localhost/info.php` no navegador. Se as informações do PHP aparecerem, a configuração está correta.

---

## Documentação dos Endpoints

### `POST /?endpoint=cadastrarUsuario`
- **Descrição**: Cadastra um novo usuário.
- **Parâmetros no Corpo (JSON)**:
  - `nome`: Nome do usuário.
  - `email`: Email do usuário.
  - `senha`: Senha do usuário.

### `POST /?endpoint=cadastrarMotorista`
- **Descrição**: Cadastra um novo motorista.
- **Parâmetros no Corpo (JSON)**:
  - `nome`: Nome do motorista.
  - `email`: Email do motorista.
  - `senha`: Senha do motorista.
  - `placa`: Placa do veículo.
  - `modelo`: Modelo do veículo.

### `POST /?endpoint=solicitarCorrida`
- **Descrição**: Solicita uma nova corrida.
- **Parâmetros no Corpo (JSON)**:
  - `usuario_id`: ID do usuário solicitante.
  - `origem`: Ponto de partida.
  - `destino`: Destino da corrida.

### `POST /?endpoint=atribuirMotorista`
- **Descrição**: Atribui um motorista a uma corrida.
- **Parâmetros no Corpo (JSON)**:
  - `corrida_id`: ID da corrida.

### `POST /?endpoint=finalizarCorrida`
- **Descrição**: Finaliza uma corrida.
- **Parâmetros no Corpo (JSON)**:
  - `corrida_id`: ID da corrida.

### `GET /?endpoint=buscarDetalhesCorrida&corrida_id={ID}`
- **Descrição**: Busca os detalhes de uma corrida.
- **Parâmetros na URL**:
  - `corrida_id`: ID da corrida.

---

## O Que Será Avaliado

### 1. **Organização e Estrutura do Código**
- Avaliaremos a capacidade de reorganizar e estruturar o código de forma clara e eficiente.
- Esperamos que o candidato demonstre domínio sobre organização de projetos em termos de arquivos, pastas e responsabilidades de cada componente.

### 2. **Segurança**
- O código deve ser analisado para garantir que vulnerabilidades comuns, como SQL Injection e armazenamento inseguro de dados, sejam mitigadas.
- Buscamos práticas que protejam tanto os dados dos usuários quanto a integridade do sistema.

### 3. **Eficiência do Sistema**
- A avaliação inclui otimizações feitas no desempenho geral do sistema.
- Consideramos tanto a eficiência das consultas ao banco quanto o uso de recursos como cache e processos assíncronos. Sinta-se livre para mudar o tipo de cada campo no banco de dados, criar mais tabelas, utilizar bancos NoSQL como Redis, Memcached, etc.

### 4. **Capacidade de Implementação de Funcionalidades**
- Será avaliada a habilidade de estender o sistema, criando novas funcionalidades ou aprimorando as já existentes.
- Valorizamos a criatividade em propor e implementar melhorias que beneficiem o sistema como um todo.

### 5. **Testes e Confiabilidade**
- Analisaremos se o sistema foi desenvolvido com testes automatizados adequados.
- Buscamos garantir que as alterações introduzidas não comprometam funcionalidades existentes.

### 6. **Uso de Logs**
- Serão avaliadas as práticas de logging utilizadas no sistema.
- Esperamos que o sistema registre eventos importantes, como erros, acessos e alterações críticas.
- A implementação deve permitir rastrear problemas e monitorar o desempenho da aplicação.
- Valorizamos o uso de boas práticas com logs formatados (JSON estruturado, níveis de log como INFO, ERROR, DEBUG).

---

## Contexto do Teste

Imagine o seguinte cenário: você acaba de ingressar como desenvolvedor em uma empresa que gerencia uma plataforma de corridas similar ao Uber. O time de tecnologia está sobrecarregado, e você foi encarregado de assumir um sistema legado que apresenta sérios desafios.

O sistema atual, criado às pressas no início da operação, sofre com problemas de segurança, baixa eficiência e uma estrutura de código difícil de manter. Além disso, os usuários começaram a reportar problemas de lentidão nas requisições e erros ao tentar solicitar corridas.

Como parte de seu onboarding, a liderança técnica solicitou que você analisasse uma versão reduzida do sistema e demonstrasse como resolver os problemas encontrados. Este exercício deve simular o que seria sua atuação no dia a dia da empresa, apresentando melhorias significativas em um curto período.

Você receberá acesso ao código original, que inclui uma API funcional, mas com várias oportunidades de melhoria. A partir desse ponto, você será avaliado em como identifica e implementa soluções para tornar o sistema mais robusto, eficiente e seguro.

Dicas para sucesso:
- Priorize as melhorias que trarão o maior impacto imediato no desempenho e na segurança.
- Documente suas decisões de forma clara para que possamos entender sua abordagem.
- Considere o cenário de uso real: a solução precisa ser viável para um sistema em produção.

Boa sorte! Estamos empolgados para ver como você transforma este sistema.
