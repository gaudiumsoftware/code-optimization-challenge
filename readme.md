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
