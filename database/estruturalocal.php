<?php


/**
 * Dados para a API
 * 
 * descricao	Descrição do lançamento	Requerido
 * conta_id **	ID da conta bancária	Requerido
 * categoria_id	** ID da categoria	Requerido
 * valor	Use negativo para despesa e positivo para receita. Ex.: -10.00 e 10.00	Requerido
 * data_vencimento	Data de vencimento do lançamento	Requerido
 * data_pagamento	Data do pagamento. Indica que o lançamento está pago	Opcional
 * data_competencia	Data da competência. Data que indica a efetiva data do recebimento.	Opcional
 * centro_custo_lucro_id **	ID do centro de custo e lucro	Opcional
 * forma_pagamento_id **	ID da forma de pagamento	Opcional
 * pessoa_id **	ID do cliente no caso de Receita e ID do fornecedor no caso de Despesa	Opcional
 * tipo_documento_id **	ID do tipo de documento	Opcional
 * total_repeticoes	Número de vezes que o lançamento será repetido	Opcional
 * observacao	Observação do lançamento	Opcional
 * itens_adicionais[]	Itens adicionais para criar lançamento composto
 * 
 * descricao	Descrição do item	Requerido
 * categoria_id	ID da categoria	Requerido
 * valor	Somente é permitido valores positivos	Requerido
 * centro_custo_lucro_id	ID do centro de custo e lucro	Opcional
 * forma_pagamento_id	ID da forma de pagamento	Opcional
 * pessoa_id	ID do cliente no caso de Receita e ID do fornecedor no caso de Despesa
 */

/** 
 * tabela FATURAS
 * id - autoincrement
 * documento - cnpj cliente 
 * nNF - numero da NF
 * chave - chave 44 digitos
 * duplicata  - numero da duplicata
 * data_vencimento - data de vencimento
 * valor - valor 
 * create_at
 * update_at
 */

/**
 * tabela COMPRAS
 * id 
 * documento
 * nNF
 * chave
 * fatura_valor
 * duplicata
 * vencimento
 * valor
 * create_at
 * update_at
 */

