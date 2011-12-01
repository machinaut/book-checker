#!/usr/bin/env python
# condense dictionary into JSON so it's faster to read
import json,sys

def condense(infile):
    # read in the file
    dictionary = dict() # irony :-)
    # peek at the first word, check that its not already JSON-ified
    word = infile.readline().strip()
    if word.find('{') >= 0: exit(1)
    # we're good, add it and read the rest of the words
    dictionary[word] = 1
    for word in infile.readlines():
        word = word.strip()
        if word in dictionary:
            dictionary[word] += 1
        else:
            dictionary[word] = 1
    # return what we made
    return dictionary

if __name__ == "__main__":
    # write out the condensed dictionary
    dictionary = condense(sys.stdin)
    sys.stdout.write(json.dumps(dictionary,separators=(',', ':')))
