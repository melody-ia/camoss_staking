let web3;
let erc20_contract;
var erc20_abi = [{"constant":false,"inputs":[{"name":"newSellPrice","type":"uint256"},{"name":"newBuyPrice","type":"uint256"}],"name":"setPrices","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_value","type":"uint256"}],"name":"burn","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"sellPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"target","type":"address"},{"name":"mintedAmount","type":"uint256"}],"name":"mintToken","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_value","type":"uint256"}],"name":"burnFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"buyPrice","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"buy","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"frozenAccount","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"},{"name":"_extraData","type":"bytes"}],"name":"approveAndCall","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"address"}],"name":"allowance","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"amount","type":"uint256"}],"name":"sell","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"target","type":"address"},{"name":"freeze","type":"bool"}],"name":"freezeAccount","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"inputs":[{"name":"initialSupply","type":"uint256"},{"name":"tokenName","type":"string"},{"name":"tokenSymbol","type":"string"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":false,"name":"target","type":"address"},{"indexed":false,"name":"frozen","type":"bool"}],"name":"FrozenFunds","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_owner","type":"address"},{"indexed":true,"name":"_spender","type":"address"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":false,"name":"value","type":"uint256"}],"name":"Burn","type":"event"}]

const get_endpoint = function() { return WEB3_ENDPOINT; }
const get_contract_address = function() { return TokenContract; }

const initial_web3 = function() {


  let ethereum_endpoint = get_endpoint();
  let contract_address = get_contract_address();

  // console.log(`JS Inintial WEB3 : ${ethereum_endpoint}\nToken Contract : ${contract_address}`);
  // if (typeof web3 !== 'undefined') {
    // web3 = new Web3(web3.currentProvider);
  // } else {
    web3 = new Web3(new Web3.providers.HttpProvider(ethereum_endpoint));
    erc20_contract = new web3.eth.Contract(erc20_abi, contract_address);
  // }
}

const create_account = function() {
  account = web3.eth.accounts.create();
  return {
    address: account.address,
    privateKey: account.privateKey
  };
}

const get_balanceOf = function(address, callback) {
  erc20_contract.methods.balanceOf(address).call().then(res => {
    return callback(res);
  });
};







const estimate_gas = function(from_address,to_address,contractAddress,tokenDecimal,originAmount,callback){ // 추가

  if(to_address == "" || originAmount == "")
    return ;

  var tokenPrice;
    if(originAmount > 999){
      tokenPrice = new web3.utils.BN(tokenDecimal).mul(new web3.utils.BN(originAmount));
      // console.log("1000이상");
    }else{
      tokenPrice = originAmount * tokenDecimal;
      // console.log("999이하");
      // console.log(tokenPrice);
    }

  let estimate_data = erc20_contract.methods.transfer(to_address, tokenPrice).encodeABI();

  web3.eth.estimateGas({

    from: from_address,
    to: contractAddress,
    data: estimate_data

  }).then(estimateGas =>{
      return callback(estimateGas,estimate_data);
    });
}



const send_token = function(from_address, to_address, contract_address, tokenDecimal,originAmount,from_key,gasPrice,callback) {
  var tokenPrice;
  if(originAmount > 999){
    tokenPrice = new web3.utils.BN(tokenDecimal).mul(new web3.utils.BN(originAmount));
    // console.log("1000이상");
  }else{
    tokenPrice = originAmount * tokenDecimal;
    // console.log("999이하");
  }

  let data = erc20_contract.methods.transfer(to_address, tokenPrice).encodeABI();

  const nonce = web3.eth.getTransactionCount(from_address).then(console.log);

  web3.eth.estimateGas({

  from: from_address,
  to: contract_address,
  data: data

}).then(estimateGas =>{

  let txdata = {
    "from": from_address,
    "to": contract_address,
    "gas": web3.utils.toHex(estimateGas),
    "value": "0x00",
    "gasPrice": web3.utils.toWei(gasPrice, 'gwei'),
    "data": data
  };



  web3.eth.accounts.signTransaction(txdata, from_key)
  .then(signed => {
    web3.eth.sendSignedTransaction(signed.rawTransaction)

    .once('transactionHash', function(hash) {
    // console.log("hash"+hash);
    callback(null, "success:"+hash);
   })
   .on('confirmation', function(confNumber, receipt) { console.log(confNumber, receipt) })
   .on('error', error => {return callback(error, null); })
  })

});

};


//결제
const send_token_for_pay = function(from_address, to_address, contract_address, tokenDecimal,originAmount,from_key,gasPrice,estimateGas,callback) {
  var tokenPrice;
  if(originAmount > 999){
    tokenPrice = new web3.utils.BN(tokenDecimal).mul(new web3.utils.BN(originAmount));
    // console.log("1000이상");
  }else{
    tokenPrice = originAmount * tokenDecimal;
    // console.log("999이하");
  }

  let data = erc20_contract.methods.transfer(to_address, tokenPrice).encodeABI();

  const nonce = web3.eth.getTransactionCount(from_address).then(console.log);

  let txdata = {
    "from": from_address,
    "to": contract_address,
    "gas": web3.utils.toHex(estimateGas),
    "value": "0x00",
    "gasPrice": web3.utils.toWei(gasPrice, 'gwei'),
    "data": data
  };



  web3.eth.accounts.signTransaction(txdata, from_key)
  .then(signed => {
    web3.eth.sendSignedTransaction(signed.rawTransaction)

    .once('transactionHash', function(hash) {
    // console.log("hash"+hash);
    callback(null, "success:"+hash);
   })
   .on('confirmation', function(confNumber, receipt) { console.log(confNumber, receipt) })
   .on('error', error => {return callback(error, null); })
  })


};









$('#send_token button').click(function() {
  from = $('#from_address').val();
  to = $('#to_address').val();
  gas_limit = $('#gas_limit').val();
  amount = $('#amount').val();

  if (!from || !to || !gas_limit) {
    alert('there are empty values!');
    return false;
  }

  $this = $(this);
  $this.siblings('#result').text('processing...');
  send_token(from, to, gas_limit, amount, (error, res) => {
    if (error) {
      $this.siblings('#result').text(JSON.stringify(error, null, 2));
    } else {
      /**
       * Token을 송금한 이후에 호출되지만 발생된 Transaction의 Confirm은 계속 증가한다.
       */
      $this.siblings('#result').text(JSON.stringify(res, null, 2));
    }
  });
});
