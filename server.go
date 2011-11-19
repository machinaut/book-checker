// server.go - quick'n'dirty webserver for book-checker
//   Serves up a simple page w/ jquery, user provides link to 
//   a book/text file.  Then reads that book for words.
//   
package main

import (
	"fmt"
	"io"
	"http"
	"os"
)

func admin(c *http.Conn, req *http.Request) {
	resstring := fmt.Sprintf("<p>The id is %s</p>", "moocow")
	io.WriteString(c, resstring)
}

func banana(c *http.Conn, req *http.Request) {
	contents, err := io.ReadFile("index.html")
	if err != nil {
		panic("ListenAndServe: ", err.String())
	}
	io.WriteString(c, string(contents))
}

func main() {
	http.Handle("/admin", http.HandlerFunc(admin))
	http.Handle("/banana", http.HandlerFunc(banana))
	err := http.ListenAndServe(":11118", nil)
	if err != nil {
		panic("ListenAndServe: ", err.String())
	}
}
