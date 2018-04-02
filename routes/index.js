const express = require('express');
const router = express.Router();
const path = require('path');
const fs = require('fs-extra');

const _showFiles = require('../src/files');

const config = require('../config');
const storageFolderPath = config.storageFolder;


router.get('/', (req, res, next) => {
  _showFiles(res).catch(err => {
      console.log(err);
    });
});

router.post('/', (req, res, next) => {

  req.pipe(req.busboy); // Pipe it trough busboy

  req.busboy.on('file', (fieldname, file, filename) => {
    console.log(`Upload of '${filename}' started`);

    // Create a write stream of the new file
    const fstream = fs.createWriteStream(path.join(storageFolderPath, filename));
    // Pipe it trough
    file.pipe(fstream);

    // On finish of the upload
    fstream.on('close', () => {
      console.log(`Upload of '${filename}' finished`);
      res.redirect('back');
    });
  });
});

module.exports = router;