const express = require('express');
const router = express.Router();
const path = require('path');
const fs = require('fs-extra');
const storageFolder = path.join(__dirname, '..', 'uploaded_files/');

router.get('/', function (req, res, next) {
  const fileName = req.query.file;
  const filePath = path.join(storageFolder, req.query.file);

  if (fileName) {
    console.log(`Going to remove: ${filePath}`);

    try {
      fs.unlinkSync(filePath);
      console.log(`Removed ${filePath}`);
    } catch (err) {
      console.error(err);
      res.redirect('/')
    }
    res.redirect('/');
  } else {
    res.redirect('/');
  }

});

module.exports = router;
