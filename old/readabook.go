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
	bc "bookchecker"
	"flag"
	"fmt"
)


func main() {
	flag.Parse() // get commandline arguments
	if flag.NArg() < 1 {
		fmt.Printf("Error: missing argument bookfilename\n")
		return
	}
	filename := flag.Arg(0)
	b, err := bc.NewBookFile(filename)
	if nil != err {
		fmt.Print("Error Reading Book:", err)
		return
	}
	fmt.Print(b.MakeDict())
}
