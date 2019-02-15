<?php

namespace Chelout\RelationshipEvents;

use Chelout\RelationshipEvents\Contracts\EventDispatcher;
use Chelout\RelationshipEvents\Traits\HasEventDispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToBase;

class MorphTo extends MorphToBase implements EventDispatcher
{
    use HasEventDispatcher;

    /**
     * Associate the model instance to the given parent.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function associate($model)
    {
        if ($this->parent->fireModelMorphToEvent('associating', $this->relation, $model) === false) {
            return false;
        }

        $result = parent::associate($model);

        $this->parent->fireModelMorphToEvent('associated', $this->relation, $model);

        return $result;
    }

    /**
     * Dissociate previously associated model from the given parent.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dissociate()
    {
        $parent = $this->getResults();

        if ($this->parent->fireModelMorphToEvent('dissociating', $this->relation, $parent) === false) {
            return false;
        }

        $result = parent::dissociate();

        if (! is_null($parent)) {
            $this->parent->fireModelMorphToEvent('dissociated', $this->relation, $parent);
        }

        return $result;
    }

    /**
     * Update the parent model on the relationship.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function update(array $attributes)
    {
        $related = $this->getResults();

        if ($this->parent->fireModelMorphToEvent('updating', $this->relation, $related) === false) {
            return false;
        }

        if ($related && $result = $related->fill($attributes)->save()) {
            $this->parent->fireModelMorphToEvent('updated', $this->relation, $related);
        }

        return $result;
    }
}
