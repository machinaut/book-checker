// book.go - reads a book for words and frequencies
package main

import (
	"bufio"
    "json"
	"os"
	"unicode"
)

type Book struct {
	*bufio.Reader
}
// Make a new book and return it
func NewBook(br *bufio.Reader) (b *Book) {
	return &Book{br}
}
// Make a new book from a file and return it
func NewBookFile(filename string) (b *Book, err os.Error) {
	f, err := os.Open(filename) // TODO: f.Close() this eventually
	if nil != err {
		return nil, err
	}
	return NewBook(bufio.NewReader(f)), nil
}

// Read the next run of unicode letters
func (b *Book) ReadWord() (word string, err os.Error) {
	// search for start of a new word
	r := 0 // rune
	for !unicode.IsLetter(r) {
		if nil != err {
			return "", err
		}
		r, _, err = b.ReadRune()
	}
	// now read to end of word
	aword := make([]int, 0)
	for unicode.IsLetter(r) {
		if os.EOF == err {
			return string(aword), nil
		} else if nil != err {
			return "", err
		}
		aword = append(aword, unicode.ToLower(r))
		r, _, err = b.ReadRune()
	}
	// return the word
	return string(aword), nil
}

// Make a dictionary with words and frequencies
func (b *Book) MakeDict() (dict map[string]int, err os.Error) {
	dict = make(map[string]int)
	for nil == err {
		var word string
		word, err = b.ReadWord()
		if word != "" { // add word to dictionary
			count, present := dict[word]
			if present {
				dict[word] = count + 1
			} else {
				dict[word] = 1 // first instance
			}
		}
	}
	if err != nil && err != os.EOF {
		return nil, err
	}
	return dict, nil
}

func main() {
    b, _ := NewBookFile("2701.txt.utf8")
    d, _ := b.MakeDict()
    e := json.NewEncoder(os.Stdout)
    e.Encode(d)
}

