# api.pieam.dev

## MVP

<https://docs.google.com/document/d/15oGaH2ACjKr4ldHB0rhYRjClO6Zte4oxDpFaJ9i9IsE/edit>

## Infra and Arch

<https://miro.com/app/board/uXjVN-4oBSA=/>

## OBS

The dependency injection have 2 function:

- inject the class from previous layer
- facility to use container. In this case may have cases that class are in the same layer but, have another classes injected on constructor

## TODO

- Validate the Password Algorithm on Access with the prefix on hash saved value
- Enumerate requests for tracing
- retry for unsuccessful maked requests, like error to insert data on database, or message on queue
- para verificar se uma tarefa está aberta por outra pessoa, o fluxo é o seguinte:
  - consulta no cache se a tarefa está marcada como aberta
    - caso não esteja aberta
      - define no cache que a tarefa está aberta pelo usuário X
      - consulta os dados no banco
      - retrna para o usuário
    - caso esteja aberta
      - busca as infos no banco
      - verifica novamente no cache se a tarefa está aberta
        - caso esteja aberta
          - retorna os dados com bloqueio de ediçõa
        - caso não esteja aberta por outra pessoa
          - marca tarefa no cache como aberta
          - retorna os dados livremente

## Todos pattern

this is for mapping the tech debt into code

`@todo[{KIND}]`

### example

`@todo[LOG]`

### mapped todos

`@todo[LOG]`

`@todo[FLOW]`

`@todo[FRONT]`

`@todo[SESSION]`

`@todo[CRYPT]`

`@todo[CSRF]`

`@todo[OUTPUT]`
