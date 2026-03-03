import { ethers } from "ethers";

const wallet = {

    async connect() {
        if (!window.ethereum) {
            throw new Error("MetaMask not installed");
        }

        const provider = new ethers.providers.Web3Provider(window.ethereum);
        await provider.send("eth_requestAccounts", []);
        const signer = provider.getSigner();
        const address = await signer.getAddress();

        return { provider, signer, address };
    },

    async sign(message) {
        const { signer, address } = await this.connect();
        const signature = await signer.signMessage(message);

        return { address, signature };
    }
};

export default wallet;