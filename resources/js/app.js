import { ethers } from "ethers";
import wallet from "./wallet";

// Optionally expose globally so Blade scripts can use it
window.ethers = ethers;
window.wallet = wallet;
require('./bootstrap');
