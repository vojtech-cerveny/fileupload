A simple nodejs file server for private networks. Recommended to install only on TRUSTED network!

# Hightlight
- Easy for sharing files (from touch devices to your PC)
- No limitations for upload
- Easy deleting unnecessary files

# The requirements
- Node.js

# Install and run
- `yarn install`
- `yarn start`
- App is available on `localhost:3200`

# Docker build and run
- $ docker build -t <your-tag-here> .
- $ docker run -p 3200:3200 -v <your-path>:/usr/src/app/uploaded_files <your-tag>