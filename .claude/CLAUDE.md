# Regras do Projeto

# Contexto

Esse é um projeto PHP com laravel, que tem como objetivo listar os players de um jogo online
Atualmente temos 1 scheduler que fica batendo requisições em uma página específica e de lá ele busca os chares online e salva esses dados no banco.

Rodando em paralelo, tenho uma visualização, aonde é listado os chares online e tem algumas opções na tela.

As features dessa tela são:
 - Copiar o exiva de um player para saber a localização dele no jogo
 - Avisar quando muitos chares bomba/bombao/maker logarem ao mesmo tempo
 - Avisar quando muitos chares main deslogarem ao mesmo tempo
 - Mostrar a quanto tempo online o player está

## Planejamento
- Sempre que for solicitado para criar um plano, quebre em etapas numeradas e sequenciais, com descrição clara do que será feito em cada uma.

## Alterações em código
- Utilize boas práticas de programação, como SOLID, Object Calisthenics e Clean Code
- Toda alteração no código precisa ter testes
- Toda alteração precisa ter testes que seguem o padrão estipulado anteriormente.
- Não escreva regras de negócio em controllers, sempre crie uma função no service com o nome do model
- Retornos json devem estar em classes JsonResource, não dentro do controller como array normal
- 
