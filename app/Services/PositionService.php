<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Masters\Position;
use App\Models\UserProfile;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PositionService
{
    public function __construct()
    {
        
    }
    
    public function generateApprovalSequence(): array
    {
        $approvalTree = $this->buildPositionTree();

        $results = [];
        if(!empty($approvalTree)){
            $this->approvalTreeToSequence($approvalTree[0], 1, $results);
            $this->fillEmployeeIntoSequence($results);
        }

        return $results;
    }

    private function fillEmployeeIntoSequence($sequence){
        foreach($sequence as &$loop){
            $employees = UserProfile::whereHas('positions', function($query) use($loop) {
                $query->where('mx_employee_position.position_id', $loop['approver_position_id']);
            })->get();

            if(count($employees) == 0){
                throw new Exception('No employee set for position_id: '.$loop['approver_position_id']);
            }

            $loop['approver_user_id'] = $employees[0]->user_id;
        }
        unset($loop);
    }

    private function approvalTreeToSequence($node, $level, &$results){
        if(!empty($node['childs'])){
            $position_id = $node['position_id'];
            $employees = UserProfile::whereHas('positions', function($query) use($position_id) {
                $query->where('mx_employee_position.position_id', $position_id);
            })->get();

            $results []= [
                'level' => $level,
                'approver_user_id' => count($employees)>0? $employees[0]->user_id: null,
                'approver_position_id' => $position_id
            ];

            foreach($node['childs'] as $child){
                $this->approvalTreeToSequence($child, $level+1, $results);
            }
        }
    }
    
    private function buildPositionTree(): array
    {
        // Get all positions that are not disabled
        $positions = Position::where('is_disabled', false)
            ->orderBy('name')
            ->get();

        if(empty($positions)){
            return [];
        }

        // Find the root positions (those without parent)
        $rootPositions = $positions->whereNull('parent_position_id');

        // Build the hierarchical structure
        $result = [];
        foreach ($rootPositions as $rootPosition) {
            $result[] = $this->buildPositionTree_callback($rootPosition, $positions);
        }

        return $result;
    }

    /**
     * Recursively build the position tree structure
     */
    private function buildPositionTree_callback(Position $position, Collection $allPositions): array
    {
        $node = [
            'position_id' => $position->position_id,
            'name' => $position->name,
            'description' => $position->description,
            'parent_position_id' => $position->parent_position_id,
            'childs' => []
        ];

        // Find direct children of the current position
        $children = $allPositions->where('parent_position_id', $position->position_id);

        foreach ($children as $child) {
            $node['childs'][] = $this->buildPositionTree_callback($child, $allPositions);
        }

        return $node;
    }
}