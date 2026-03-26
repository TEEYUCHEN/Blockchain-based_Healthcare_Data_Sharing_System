let web3;
let contract;
let userWallet;

async function initWeb3(abi, address) {

  if (!window.ethereum) {
    alert("MetaMask not installed");
    return;
  }

  web3 = new Web3(window.ethereum);

  const accounts = await window.ethereum.request({
    method: "eth_requestAccounts",
  });

  userWallet = accounts[0];

  contract = new web3.eth.Contract(abi, address);
}

async function grantAccess(targetWallet) {
  return await contract.methods.grantAccess(targetWallet).send({
    from: userWallet,
  });
}

async function revokeAccess(targetWallet) {
  return await contract.methods.revokeAccess(targetWallet).send({
    from: userWallet,
  });
}

window.initWeb3 = initWeb3;
window.grantAccess = grantAccess;
window.revokeAccess = revokeAccess;