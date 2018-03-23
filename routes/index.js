const express = require('express');
const router = express.Router();
const path = require('path');
const fs = require('fs');
const _ = require('lodash');
const diskspace = require('diskspace');
const multer = require('multer');
const mime = require('mime-types');

const uploadedFiles = path.join(__dirname, '..', 'uploaded_files/');
const location = 'D'

const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, uploadedFiles)
  },
  filename: function (req, file, cb) {
    cb(null, file.fieldname + '-' + Date.now())
  }
})
const multerFiles = multer({ storage: storage })

// Transform bytes to human format
const sizeOf = function (bytes) {
  if (bytes == 0) {
    return "0.00 B";
  }
  const e = Math.floor(Math.log(bytes) / Math.log(1000));
  return (bytes / Math.pow(1000, e)).toFixed(2) + '' + ' KMGTP'.charAt(e) + 'B';
};

// File type icons 
function fileTypeIcon(type){
  if(!type){
    return 'warning';
  }
  if(type.includes('image') && !type.includes('gif')){
    return 'image';
  }
  if(type.includes('x-msdos-program')){
    return 'play_circle_outline';
  }
  if(type.includes('pdf')){
    return 'picture_as_pdf';
  }
  if(type.includes('text')){
    return 'text_format';
  }
  if(type.includes('gif')){
    return 'gif';
  }
  if(type.includes('video')){
    return 'ondemand_video';
  }
  if(type.includes('zip')){
    return 'archive';
  }
  return 'warning';
}

// 
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
            'intSize': stats['size'],
            'created': stats['birthtime'],
            'icon': fileTypeIcon(mime.lookup(files[i]))
          });
        });
      }));
    }
    return Promise.all(innerPromises);

  }).then(files => {

    return new Promise((resolve, reject) => {
      const filesAndFreeSpace = {}      
      filesAndFreeSpace.files = files;
      filesAndFreeSpace.size = {};
      filesAndFreeSpace.size.pdfCount = 0;
      filesAndFreeSpace.size.imageCount = 0;
      filesAndFreeSpace.size.textCount = 0;
      filesAndFreeSpace.size.gifCount = 0;
      filesAndFreeSpace.size.videoCount = 0;
      filesAndFreeSpace.size.zipCount = 0;
      filesAndFreeSpace.size.exeCount = 0;
      filesAndFreeSpace.size.otherCount = 0;
      filesAndFreeSpace.size.pdfSize = 0;
      filesAndFreeSpace.size.imageSize = 0;
      filesAndFreeSpace.size.textSize = 0;
      filesAndFreeSpace.size.gifSize = 0;
      filesAndFreeSpace.size.videoSize = 0;
      filesAndFreeSpace.size.zipSize = 0;
      filesAndFreeSpace.size.exeSize = 0;
      filesAndFreeSpace.size.otherSize = 0;
      filesAndFreeSpace.size.pdfIntSize = 0;
      filesAndFreeSpace.size.imageIntSize = 0;
      filesAndFreeSpace.size.textIntSize = 0;
      filesAndFreeSpace.size.gifIntSize = 0;
      filesAndFreeSpace.size.videoIntSize = 0;
      filesAndFreeSpace.size.zipIntSize = 0;
      filesAndFreeSpace.size.exeIntSize = 0;
      filesAndFreeSpace.size.otherIntSize = 0;
      
      filesAndFreeSpace.files.forEach(function typeSizeCalc(file){
        if( file.icon == 'picture_as_pdf'){
          filesAndFreeSpace.size.pdfIntSize += file.intSize
          filesAndFreeSpace.size.pdfCount += 1;
          filesAndFreeSpace.size.pdfSize = sizeOf(filesAndFreeSpace.size.pdfIntSize)
        }
        else if(file.icon == 'image'){         
          filesAndFreeSpace.size.imageIntSize += file.intSize
          filesAndFreeSpace.size.imageCount += 1;
          filesAndFreeSpace.size.imageSize = sizeOf(filesAndFreeSpace.size.imageIntSize)
        }
        else if(file.icon == 'text_format'){         
          filesAndFreeSpace.size.textIntSize += file.intSize
          filesAndFreeSpace.size.textCount += 1;
          filesAndFreeSpace.size.textSize = sizeOf(filesAndFreeSpace.size.textIntSize)
        }
        else if(file.icon == 'gif'){         
          filesAndFreeSpace.size.gifIntSize += file.intSize
          filesAndFreeSpace.size.gifCount += 1;
          filesAndFreeSpace.size.gifSize = sizeOf(filesAndFreeSpace.size.gifIntSize)
        }
        else if(file.icon == 'ondemand_video'){          
          filesAndFreeSpace.size.videoIntSize += file.intSize
          filesAndFreeSpace.size.videoCount += 1;
          filesAndFreeSpace.size.videoSize = sizeOf(filesAndFreeSpace.size.videoIntSize)
        }
        else if(file.icon == 'archive'){         
          filesAndFreeSpace.size.zipIntSize += file.intSize
          filesAndFreeSpace.size.zipCount += 1;
          filesAndFreeSpace.size.zipSize = sizeOf(filesAndFreeSpace.size.zipIntSize)
        }
        else if(file.icon == 'play_circle_outline'){          
          filesAndFreeSpace.size.exeIntSize += file.intSize
          filesAndFreeSpace.size.exeCount += 1;
          filesAndFreeSpace.size.exeSize = sizeOf(filesAndFreeSpace.size.exeIntSize)
        }
        else if(file.icon == 'warning'){          
          filesAndFreeSpace.size.otherIntSize += file.intSize
          filesAndFreeSpace.size.otherCount += 1;
          filesAndFreeSpace.size.otherSize = sizeOf(filesAndFreeSpace.size.otherIntSize)
        }
      })


      diskspace.check(location, function (err, result){
          if(err) {
            reject(err);
          } else {
            filesAndFreeSpace.freeSpace = sizeOf(result.free);
            filesAndFreeSpace.usedSpace = sizeOf(result.used);
            filesAndFreeSpace.totalSpace = sizeOf(result.total);
            filesAndFreeSpace.free = result.free;
            filesAndFreeSpace.used = result.used;        
            resolve(filesAndFreeSpace)
          }        
       });
    });       
  }).then((filesAndFreeSpace) => {     
   res.render('index', {title: 'Upload', files: _.orderBy(filesAndFreeSpace.files, ['created'], ['desc'], ['icon']), 
                                         freeSpace : filesAndFreeSpace.freeSpace, free : filesAndFreeSpace.free,
                                         usedSpace : filesAndFreeSpace.usedSpace, used : filesAndFreeSpace.used,
                                         totalSpace : filesAndFreeSpace.totalSpace,
                                         sizeLocation: location,
                                         json: filesAndFreeSpace.size
                                        });

  }).catch((err) => {
    res.render('index', {title: 'There was an error check console'});
    console.error(err)
  })
};

router.get('/', function (req, res, next) {
  _showFiles(res)
    .catch(err => {
      console.log(err);
    });
});

router.post('/',  multerFiles.single('sampleFile'), function(req, res) {
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