var express = require('express');
var router = express.Router();
const path = require('path');
const fs = require('fs');
const uploadedFiles = path.join(__dirname, '..', 'uploaded_files/');

/* GET listing. */
router.get('/', function(req, res, next) {
  if (req.query.file) {
    console.log(req.query.file);
    new Promise((resolve, reject) => {
      resolve(path.join(uploadedFiles, req.query.file))
    })
      .then(filePath => {
        console.log(filePath);
        new Promise((resolve, reject) => {
          try {
            fs.stat(filePath, (err) => {
              if (err) {
                reject(err);
              } else {
                fs.unlink(filePath);
                resolve();
              }
            });
          } catch (err) {
            console.log(err);
          }
        })
      })
      .then(()=> res.redirect('/'))
      .catch((err) => {
        console.log(err);
      })
  }
});

module.exports = router;
