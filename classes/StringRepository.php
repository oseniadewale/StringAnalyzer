<?php
class StringRepository {
    private $file;

    public function __construct($file = 'data/strings.json') {
        $this->file = $file;
        if (!file_exists($file)) file_put_contents($file, json_encode([]));
    }

    private function load() {
        return json_decode(file_get_contents($this->file), true);
    }

    private function save($data) {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function findByValue($value) {
        $data = $this->load();
        foreach ($data as $record) {
            if ($record['value'] === $value) return $record;
        }
        return null;
    }

    public function findByHash($hash) {
        $data = $this->load();
        foreach ($data as $record) {
            if ($record['id'] === $hash) return $record;
        }
        return null;
    }

    public function add($record) {
        $data = $this->load();
        $data[] = $record;
        $this->save($data);
    }

    public function getAll() {
        return $this->load();
    }

    public function deleteByValue($value) {
        $data = $this->load();
        $filtered = array_filter($data, fn($r) => $r['value'] !== $value);
        $this->save(array_values($filtered));
    }
}
