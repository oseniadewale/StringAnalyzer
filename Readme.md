#  — PHP OOP

This is a **RESTful API** built with **plain PHP OOP** that analyzes strings and returns their computed properties.  
It was developed to demonstrate object-oriented principles and clean API design without any framework.

---

##  Features Overview

The API analyzes any string and computes:

- `length` → total number of characters  
- `is_palindrome` → true if it reads the same forward and backward  
- `unique_characters` → number of distinct characters  
- `word_count` → total number of words  
- `sha256_hash` → unique hash for identification  
- `character_frequency_map` → number of times each character appears  

It also supports:
- Getting all analyzed strings  
- Retrieving specific strings  
- Filtering with query parameters  
- Natural language filtering  
- Deleting a stored string  

---

## 🔹 1. Create & Analyze String

**Endpoint:**  
`POST /strings`

**Request Body:**
```json
{
  "value": "string to analyze"
}

Examples of request:
{
  "id": "b1946ac92492d2347c6235b4d2611184",
  "value": "string to analyze",
  "properties": {
    "length": 17,
    "is_palindrome": false,
    "unique_characters": 12,
    "word_count": 3,
    "sha256_hash": "abc123...",
    "character_frequency_map": {
      "s": 2,
      "t": 3,
      "r": 2
    }
  },
  "created_at": "2025-10-21T14:30:00Z"
}

Examples of response: 
{
  "id": "a74d4b123...",
  "value": "madam",
  "properties": {
    "is_palindrome": true,
    "word_count": 1,
    "unique_characters": 3
  },
  "created_at": "2025-10-21T14:31:00Z"

