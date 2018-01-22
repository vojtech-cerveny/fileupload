const express = require('express');
const router = express.Router();
const path = require('path');
const fs = require('fs');
const async = require('async');
const _ = require('lodash');
const uploadedFiles = path.join(__dirname, '..', 'uploaded_files/');

const sizeOf = function (bytes) {
  if (bytes == 0) {
    return "0.00 B";
  }
  const e = Math.floor(Math.log(bytes) / Math.log(1000));
  return (bytes / Math.pow(1000, e)).toFixed(2) + ' ' + ' KMGTP'.charAt(e) + 'B';
};

const _showFiles = (res) => {
  return new Promise((resolve, reject) => {
    let files = [];

    try {
      if (!fs.existsSync(uploadedFiles)) {
        fs.mkdirSync(uploadedFiles);
      }
      
      fs.readdirSync(uploadedFiles).forEach(file => {
        files.push(file);
      });
      resolve(files);
    } catch (err) {
      reject(err);
    }
  }).then(files => {
    let innerPromises = [];
    // if(files == [])
    //   return 0;
    for (let i = 0; i < files.length; i++) {
      innerPromises.push(new Promise((resolve, reject) => {
        fs.stat(uploadedFiles + files[i], (err, stats) => {
          if (err) {
            reject(err);
            return;
          }

          resolve({
            'name': files[i],
            'url': '/uploaded_files/' + files[i],
            'size': sizeOf(stats['size']),
            'created': stats['birthtimeMs']
          });
        });
      }));
    }
    return Promise.all(innerPromises);

  }).then(prepared_files => {
    res.render('index', {title: 'Upload', files: _.orderBy(prepared_files, ['created'], ['desc'])});
  })
};


router.get('/', function (req, res, next) {
  _showFiles(res)
    .catch(err => {
      console.log(err);
    });
});

router.post('/', function(req, res) {
  if (!req.files)
    return res.status(400).send('No files were uploaded.');

  const sampleFile = req.files.sampleFile;

  sampleFile.mv(uploadedFiles + sampleFile.name, function(err) {
    if (err)
      return res.status(500).send(err);

    res.redirect('/');
  });
});

router.post('/', (req, res) => {
  fs.unlink(__dirname + '/uploaded_files/' + req.param.file);
  res.redirect('/');
});

module.exports = router;