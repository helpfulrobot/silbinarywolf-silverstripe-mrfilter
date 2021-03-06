<?php

if (!class_exists('SolrSearchService')) {
	return;
}

/**
 * A set of fields / values to filter the current request by
 *
 * @author marcus
 */
class ListFilterSolrFields extends ListFilterBase
{
    private static $db = array(
		'SolrFilterFields'	=> 'MultiValueField',
	);
    
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $fields->replaceField('SolrFilterFields', 
            KeyValueField::create('SolrFilterFields')
                ->setRightTitle('Solr fields to filter by; use $FieldName to take a property from the "current" page')
        );

        return $fields;
    }
    
    public function applyFilter(SS_List $list, array $data) {
		$sharedFilter = $this->SharedFilter('ListFilterSharedSolr');
		$builder = $sharedFilter->getQueryBuilder();
        
        $filters = $this->SolrFilterFields->getValues();
        if (count($filters)) {
            foreach ($filters as $field => $value) {
                if ($value{0} == '$') {
                    $curr = Controller::has_curr() ? Controller::curr() : null;
                    if ($curr instanceof ContentController) {
                        $page = $curr->data();
                        if ($page) {
                            $keyword = substr($value, 1);
                            $value = $page->hasField($keyword) ? $page->$keyword : $value;
                        }
                    }
                }
                $builder->addFilter($field, $value);
            }
        }
		
		return $sharedFilter;
	}
}
