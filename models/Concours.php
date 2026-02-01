<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Concours Model
 * Manages concours/contests
 */
class Concours extends BaseModel {
    protected $table = 'concours';
    protected $fillable = ['title', 'description', 'rules', 'prizes', 'image', 'video', 'start_date', 'end_date', 'status', 'max_participants'];

    /**
     * Validate concours data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        if (empty($data['title'])) {
            $this->addError("Le titre est requis.");
        }

        if (empty($data['description'])) {
            $this->addError("La description est requise.");
        }

        if (empty($data['start_date'])) {
            $this->addError("La date de début est requise.");
        }

        if (empty($data['end_date'])) {
            $this->addError("La date de fin est requise.");
        }

        return empty($this->errors);
    }
}
