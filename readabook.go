// readabook.go - reads a book for words and frequencies
// Example: http://www.gutenberg.org/ebooks/2701.txt.utf8
//
// Later will calculate Levenshtein distance:
// http://en.wikipedia.org/wiki/Levenshtein_distance
//
// TODO(ajray):
//   * Ignore project gutenberg headers/footers, for now 
//		they are parsed with the rest of the text.
package main

import (
	"bufio"
	"flag"
	"fmt"
	"os"
	"unicode"
)

type Book struct {
	filename string
	*bufio.Reader
}
// Make a new book and return it
func NewBook(filename string) (b *Book, err os.Error) {
	f, err := os.Open(filename) // TODO: f.Close() this eventually
	if nil != err {
		return nil, err
	}
	r := bufio.NewReader(f)
	return &Book{filename, r}, nil
}

// Read the next run of unicode letters
func (b *Book) ReadWord() (word []int, err os.Error) {
	// search for start of a new word
	word = make([]int, 0)
	r := 0
	for !unicode.IsLetter(r) {
		if os.EOF == err {
			return word, err
		} else if nil != err {
			return nil, err
		}
		r, _, err = b.ReadRune()
	}
	// now read to end of word
	word = append(word, r)
	for unicode.IsLetter(r) {
		if os.EOF == err {
			return word, err
		} else if nil != err {
			return nil, err
		}
		word = append(word, r)
		r, _, err = b.ReadRune()
	}
	// return the word
	return word, os.EOF // os.EOF is end signal
}

func (b *Book) MakeDict() (dict map[string]int, err os.Error) {
	// iterate over words
	dict = make(map[string]int)
	for nil == err {
		word, err := b.ReadWord()
		if nil != word { // add word to dictionary
			sword := string(word)
			count, present := dict[sword]
			if present {
				dict[sword] = count + 1
			} else {
				dict[sword] = 1 // first instance
			}
		}
		if os.EOF == err {
			break
		} else if nil != err {
			return nil, err
		}
	}
	return dict, nil
}

func main() {
	flag.Parse() // get commandline arguments
	if flag.NArg() < 1 {
		fmt.Printf("Error: missing argument <bookfilename>\n")
		return
	}
	filename := flag.Arg(0)
	b, err := NewBook(filename)
	if nil != err {
		fmt.Print("Read Error:", err)
		return
	}
	fmt.Print(b.MakeDict())
}
