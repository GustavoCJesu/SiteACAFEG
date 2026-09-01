# ☕ ACAFEG — Do Solo Vulcânico à Sua Xícara

Site institucional para a **ACAFEG** (Associação dos Cafeicultores do Sul de Minas Gerais), apresentando a história da associação, seus associados, projetos e cafés especiais cultivados em Andradas, MG.

## ✨ Funcionalidades

- **Múltiplas páginas**: Home (A Associação), Quem Somos, Associados, Nosso Café e Contato.
- **Perfis dos associados**, com fotos e histórias das famílias produtoras.
- **Apresentação de projetos** da associação (ex.: Mulheres ACAFEG, Cafeicultura Regenerativa), com documentos em PDF disponíveis para download.
- **Formulário de contato** com validação e envio processados em **PHP** no backend.
- **Consentimento LGPD** obrigatório no envio do formulário.
- **Proteção anti-spam** contra header injection no backend.
- Design com tipografia elegante ([Cormorant Garamond](https://fonts.google.com/specimen/Cormorant+Garamond) + [DM Sans](https://fonts.google.com/specimen/DM+Sans)) e layout responsivo.

## 🛠️ Tecnologias

- **HTML5** semântico
- **CSS3** (estilos organizados por página)
- **JavaScript** (interações e navegação)
- **PHP** — processamento e validação do formulário de contato no servidor
- Google Fonts

## 📁 Estrutura do projeto

```
index.html              # Home / A Associação
pages/
├── aboutUs.html          # Quem somos
├── associates.html       # Associados
├── store.html            # Nosso Café
└── contact.html          # Contato

style/                   # CSS de cada página
img/
├── associados/            # Fotos das famílias produtoras
├── Logos/                 # Selos e certificações
├── cafes/                 # Fotos do café
└── favicon/

docs/                    # PDFs dos projetos da associação
emailForms.php           # Processador do formulário de contato
```

## 🚀 Como rodar localmente

Por ser um site estático com um endpoint em PHP, é necessário um servidor com suporte a PHP.

```bash
# Clonar o repositório
git clone https://github.com/GustavoCJesu/SiteACAFEG.git
cd SiteACAFEG

# Rodar com o servidor embutido do PHP
php -S localhost:8000
```

Acesse [http://localhost:8000](http://localhost:8000).

> O formulário de contato (`emailForms.php`) envia as mensagens por e-mail; para testá-lo localmente é necessário configurar uma função de envio de e-mail (ex.: `sendmail`) ou ajustar o script para um serviço SMTP de sua preferência.

## 👤 Autor

**Gustavo Jesuino**
[LinkedIn](https://linkedin.com/in/gustavojesuino0411) · [GitHub](https://github.com/GustavoCJesu)
