<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Team Model
 * Manages team members
 */
class Team extends BaseModel {
    protected $table = 'team';
    protected $fillable = ['name', 'position', 'bio', 'image', 'speciality'];

    /**
     * Validate team member data
     */
    protected function validate($data, $id = null) {
        $this->clearErrors();

        if (empty($data['name'])) {
            $this->addError("Le nom est requis.");
        }

        if (empty($data['position'])) {
            $this->addError("Le poste est requis.");
        }

        return empty($this->errors);
    }
}
