# Instrições para execução do Projeto

O projeto foi feito utilizando o framework Laravel. As instruções de instalação estão nesse link:https://laravel.com/docs/5.6#installation

Após a instalação do Laravel abra o terminal e vá para o diretório do projeto.
```bash
#Instalar as dependencias
composer install

#Executar o projeto
php artisan serve

#O terminal exibirá a mensagem de confirmação
Laravel development server started: <http://127.0.0.1:8000> 
```

Acesse o projeto através da url http://localhost:8000

O projeto foi desenvolvido de modo que a api rode o algoritmo de classificação a cada busca para facilitar os testes.

### Documentação da API

A rota para a API é "/api/tickets/search" e os parâmetros de busca são adicionados à url.
Para que o arquivo "tickets.json" seja acessado, o mesmo deve estar no caminho "{pasta do projeto}/storage/app".


```bash

http://localhost:8000/api/tickets/search/{prioridade}

http://localhost:8000/api/tickets/search/{data inicial}/{data final}

http://localhost:8000/api/tickets/search/{data incial}/{data final}/{prioridade}

http://localhost:8000/api/tickets/search/{order by}

http://localhost:8000/api/tickets/search/{prioridade}/{order by}

http://localhost:8000/api/tickets/search/{data inicial}/{data final}/{order by}

http://localhost:8000/api/tickets/search/{data incial}/{data final}/{prioridade}/{order by}

http://localhost:8000/api/tickets/search/{numero de registros}/{numero da página}

http://localhost:8000/api/tickets/search/{prioridade}/{numero de registros}/{numero da página}

http://localhost:8000/api/tickets/search/{prioridade}/{order by}/{número de registros}/{número da página}

http://localhost:8000/api/tickets/search/{data incial}/{data final}/{número de registros}/{número da página}

http://localhost:8000/api/tickets/search/{data incial}/{data final}/{prioridade}/{order by}

http://localhost:8000/api/tickets/search/{order by}/{número de registros}/{número da página}

```
- {prioridade} para filtro por prioridade podendo ser "alta" ou "normal"
- {data inicial} e {data final} para filtro por data de criação podendo ser no formado "Y-m-d"
- {order by} podendo ser "dataatualizacao" ou "datacriacao" para ordenação.
- {número de registros} e {número da página} para paginação podendo apenas números 

# Desafio desenvolvedor backend

Precisamos melhorar o atendimento no Brasil, para alcançar esse resultado, precisamos de um algoritmo que classifique
nossos tickets (disponível em tickets.json) por uma ordem de prioridade, um bom parâmetro para essa ordenação é identificar o humor do consumidor.
Pensando nisso, queremos classificar nossos tickets com as seguintes prioridade: Normal e Alta.

### São exemplos:

### Prioridade Alta:
- Consumidor insatisfeito com produto ou serviço
- Prazo de resolução do ticket alta
- Consumidor sugere abrir reclamação como exemplo Procon ou ReclameAqui
    
### Prioridade Normal
- Primeira iteração do consumidor
- Consumidor não demostra irritação

Considere uma classificação com uma assertividade de no mínimo 70%, e guarde no documento (Nosso json) a prioridade e sua pontuação.

### Com base nisso, você precisará desenvolver:
- Um algoritmo que classifique nossos tickets
- Uma API que exponha nossos tickets com os seguintes recursos
  - Ordenação por: Data Criação, Data Atualização e Prioridade
  - Filtro por: Data Criação (intervalo) e Prioridade
  - Paginação
        
### Escolha as melhores ferramentas para desenvolver o desafio, as únicas regras são:
- Você deverá fornecer informações para que possamos executar e avaliar o resultado;
- Poderá ser utilizado serviços pagos (Mas gostamos bastante de projetos open source)
    
### Critérios de avaliação
- Organização de código;
- Lógica para resolver o problema (Criatividade);
- Performance
    
### Como entregar seu desafio
- Faça um Fork desse projeto, desenvolva seu conteúdo e informe no formulário (https://goo.gl/forms/5wXTDLI6JwzzvOEg2) o link do seu repositório
