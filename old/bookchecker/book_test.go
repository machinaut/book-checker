package bookchecker

import (
	"bufio"
	"os"
	"strings"
	"testing"
)

type wordTest struct {
	b *Book
	word string
}

var wordTests = []wordTest{
	wordTest{NewBook(bufio.NewReader(strings.NewReader("?!shim;,. 'm'y '''\name.]]["))),"shim"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("Hi my name."))),"hi"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("His's name."))),"his"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("Garçon"))),"garçon"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("I'm"))),"i"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("Yo"))),"yo"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("_=-"))),""},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("a"))),"a"},
	wordTest{NewBook(bufio.NewReader(strings.NewReader("!"))),""},
	wordTest{NewBook(bufio.NewReader(strings.NewReader(""))),""},
}

type dictTest struct {
	dict map[string] int
	text string
}

var dictTests = []dictTest{
	dictTest{map[string]int{"hi":1,"my":1,"name":2,"is":1}, "Hi! My name is NaME."},
	dictTest{map[string]int{"hi":4}, "1Hi! HI!!,:;hi\n\r  \n\t hI ."},
	dictTest{map[string]int{"ma":2,"mia":2,"må":1}, "Må Ma Mia Ma Mia."},
}

type fileTest struct {
	dict map[string] int
	filename string
}

var fileTests = []fileTest{
	fileTest{map[string]int{"my":2,"ålex":1,"alex":1,"i":1,"name":2,"s":1,"and":2},
		"test.txt.utf8"},
}

func TestReadWord(t *testing.T) {
	for _, wt := range wordTests {
		w, err := wt.b.ReadWord()
		if err != nil && err != os.EOF {
			t.Errorf("ReadWord Error on %q: %v", wt.word, err)
		}
		if w != wt.word {
			t.Errorf("ReadWord %q, expected %q.", w, wt.word)
		}
	}
}

func TestMakeDict(t *testing.T) {
	for _, dt := range dictTests {
		b := NewBook(bufio.NewReader(strings.NewReader(dt.text)))
		d,err := b.MakeDict()
		if err != nil && err != os.EOF {
			t.Errorf("MakeDict Error on %q: %v", dt.text, err)
		}
		if len(d) != len(dt.dict) {
			t.Errorf("MakeDict wrong size on %q: got %d expected %d", 
				dt.text, len(d), len(dt.dict))
		}
		for k,v := range d {
			if v != dt.dict[k] {
				t.Errorf("MakeDict(%q), for \"%s\" got %d expected %d.", 
					dt.text, k, v, dt.dict[k])
			}
		}
	}
}

func TestNewBookFile(t *testing.T) {
	for _, ft := range fileTests {
		b,err := NewBookFile(ft.filename)
		if err != nil && err != os.EOF {
			t.Errorf("NewBookFile Error on open %q: %v", ft.filename, err)
		}
		d,err := b.MakeDict()
		if err != nil && err != os.EOF {
			t.Errorf("NewBookFile Error on MakeDict %q: %v", ft.filename, err)
		}
		if len(d) != len(ft.dict) {
			t.Errorf("NewBookFile wrong size on %q: got %d expected %d", 
				ft.filename, len(d), len(ft.dict))
		}
		for k,v := range d {
			if v != ft.dict[k] {
				t.Errorf("NewBookFile(%q), for \"%s\" got %d expected %d.", 
					ft.filename, k, v, ft.dict[k])
			}
		}
	}
}
