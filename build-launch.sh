#!/bin/bash
# build and launch the go server

# prettify go code, cause i like pretty code
# also detects and stops at simple syntax errors
echo "gofmt -w readabook.go" && \
gofmt -w readabook.go && \
# compile to object files
echo "6g readabook.go" && \
6g readabook.go && \
# link to executable
echo "6l -o readabook readabook.6" && \
6l -o readabook readabook.6 && \
# run the program against test file
echo "./readabook pg2701.txt" && \
./readabook pg2701.txt && \
# Thats it! Done!
echo "build-launch.sh SUCCESS"

