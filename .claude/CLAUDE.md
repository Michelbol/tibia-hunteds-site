# Regras do Projeto

## Planejamento
- Sempre que for solicitado para criar um plano, quebre em etapas numeradas e sequenciais, com descrição clara do que será feito em cada uma.

## Pull Requests
- Todo pull request criado deve ter no máximo 250 alterações de linhas (additions + deletions). Se a implementação ultrapassar esse limite, divida em múltiplos PRs menores e sequenciais.

## Alterações em código
- Utilize boas práticas de programação, como SOLID, Object Calisthenics e Clean Code
- Toda alteração no código precisa ter testes
- Toda alteração precisa ter testes que seguem o padrão estipulado anteriormente.
- Não escreva regras de negócio em controllers, sempre crie uma função no service com o nome do model
- Retornos json devem estar em classes JsonResource, não dentro do controller como array normal
- 
