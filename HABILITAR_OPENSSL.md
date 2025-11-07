# 🔧 Como Habilitar OpenSSL no PHP

## Erro: "Extension missing: openssl"

O PHPMailer precisa da extensão OpenSSL para conexões seguras (TLS/SSL) com servidores SMTP.

---

## 📋 Solução Rápida

### **Opção 1: Habilitar OpenSSL no php.ini (Recomendado)**

1. **Encontre o arquivo php.ini:**
   ```bash
   # No terminal/CMD, execute:
   php --ini
   ```
   Isso mostrará o caminho do arquivo `php.ini`

2. **Edite o php.ini:**
   - Abra o arquivo `php.ini` em um editor de texto
   - Procure por: `;extension=openssl`
   - **Remova o ponto e vírgula** no início:
     ```ini
     ;extension=openssl  ❌ (desabilitado)
     extension=openssl   ✅ (habilitado)
     ```

3. **Salve o arquivo**

4. **Reinicie o servidor:**
   - Se usar XAMPP: Reinicie o Apache
   - Se usar WAMP: Reinicie todos os serviços
   - Se usar servidor próprio: Reinicie o PHP-FPM ou Apache

5. **Verifique se funcionou:**
   ```bash
   php -m | findstr openssl
   # Ou
   php -m | grep openssl
   ```
   Se aparecer "openssl", está funcionando!

---

### **Opção 2: Verificar se está Instalado (Windows)**

1. **Abra o php.ini:**
   - Geralmente em: `C:\xampp\php\php.ini` ou `C:\wamp\bin\php\php7.x\php.ini`

2. **Procure e descomente:**
   ```ini
   extension=openssl
   extension=curl  ; Também pode ser necessário
   ```

3. **Reinicie o Apache**

---

### **Opção 3: Verificar Extensões no Código**

Crie um arquivo `teste_php.php` na raiz do projeto:

```php
<?php
echo "Extensões carregadas:\n";
$extensoes = get_loaded_extensions();
print_r($extensoes);

echo "\n\nOpenSSL está carregado? " . (extension_loaded('openssl') ? 'SIM ✅' : 'NÃO ❌');
echo "\nCurl está carregado? " . (extension_loaded('curl') ? 'SIM ✅' : 'NÃO ❌');
?>
```

Acesse no navegador: `http://localhost/teste_php.php`

---

## 🔍 Localizar php.ini

### **Windows (XAMPP):**
```
C:\xampp\php\php.ini
```

### **Windows (WAMP):**
```
C:\wamp\bin\php\php7.x\php.ini
```

### **Linux:**
```bash
# Encontrar o php.ini
php --ini

# Ou
/etc/php/7.4/apache2/php.ini
/etc/php/8.0/cli/php.ini
```

### **Via PHP:**
Crie `info.php`:
```php
<?php phpinfo(); ?>
```
Acesse no navegador e procure por "Loaded Configuration File"

---

## ⚠️ Se Não Funcionar

### **1. Verifique se OpenSSL está instalado no sistema:**

**Windows:**
- Geralmente já vem instalado com PHP
- Se não, reinstale o PHP/XAMPP/WAMP

**Linux:**
```bash
sudo apt-get install openssl
sudo apt-get install php-openssl
```

### **2. Alternativa Temporária (NÃO Recomendado para Produção):**

Se não conseguir habilitar OpenSSL, você pode usar SMTP sem segurança (apenas desenvolvimento):

No arquivo `login/src/email_config.php`:
```php
'smtp_secure' => false,  // Sem segurança (apenas desenvolvimento!)
'smtp_port' => 25,       // Porta sem segurança
```

**⚠️ ATENÇÃO:** Isso NÃO é seguro e muitos provedores não permitem!

---

## ✅ Verificação Final

Após habilitar, teste novamente a recuperação de senha. Se funcionar, você verá:
- ✅ "Link de recuperação enviado para seu e-mail!"

---

## 🆘 Ainda Não Funciona?

1. Verifique os logs do PHP (error_log)
2. Certifique-se de que reiniciou o servidor
3. Verifique se está editando o php.ini correto (pode haver vários)
4. Tente reinstalar o PHP/XAMPP/WAMP

