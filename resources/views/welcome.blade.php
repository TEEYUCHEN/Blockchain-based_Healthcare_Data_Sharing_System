<button id="testBtn">Connect MetaMask</button>
<script src="https://cdn.jsdelivr.net/npm/ethers/dist/ethers.min.js"></script>
<script>
    document.getElementById('testBtn').addEventListener('click', async () => {
        try {
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            alert('Wallet connected: ' + accounts[0]);
        } catch (e) {
            alert('Failed to connect: ' + e.message);
            console.error(e);
        }
    });
</script>