const { contextBridge, ipcRenderer } = require('electron');

// تُستخدم من public/js/nextstore-printer.js وشاشة الإعدادات
contextBridge.exposeInMainWorld('electronAPI', {
    isElectron: true,
    getPrinters: () => ipcRenderer.invoke('ns:get-printers'),
    silentPrint: (payload) => ipcRenderer.invoke('ns:silent-print', payload),
    getMachineId: () => ipcRenderer.invoke('ns:machine-id'),
    quit: () => ipcRenderer.invoke('ns:quit'),
    getAppInfo: () => ipcRenderer.invoke('ns:app-info'),
    getNetwork: () => ipcRenderer.invoke('ns:network-get'),
    setNetwork: (n) => ipcRenderer.invoke('ns:network-set', n),
});
