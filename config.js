const path = require('path');

const storageFolder = path.join(__dirname, 'uploaded_files/');
const appPort ='3200';

module.exports = {
  storageFolder,
  appPort
};