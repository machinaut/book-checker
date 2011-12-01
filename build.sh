#!/bin/bash
# build script-basically do what the README says

# Get rid of any old dictionary/build files
echo "rm -f moby.dict moby.sparse moby.dense 2701.txt.utf8" && \
rm -f moby.dict moby.sparse 2701.txt.utf8 && \

# Download the dictionary.  Not that bad, but for future would want to check if it's here
echo "wget http://gutenberg.org/ebooks/2701.txt.utf8" && \
wget http://gutenberg.org/ebooks/2701.txt.utf8 && \

# Use aspell to figure out what is/isnt a word
echo  "aspell --encoding=utf-8 --local-data-dir=./ --lang=en clean < 2701.txt.utf8 > moby.sparse" && \
aspell --encoding=utf-8 --local-data-dir=./ --lang=en clean < 2701.txt.utf8 > moby.sparse && \

# Condense the aspell dictionary into JSON using a little python script
echo "./condense.py < moby.sparse > moby.dict" && \
./condense.py < moby.sparse > moby.dict && \

# If all went well, tell the user
echo "build.sh SUCCESS"
