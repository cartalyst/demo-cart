<?php

use Cartalyst\Conditions\Condition;
use SuperClosure\SerializableClosure;

function converter()
{
    return app('converter');
}

/**
 * Create a new condition.
 *
 * @param string $name
 * @param string $type
 * @param string $target
 * @param array $actions
 * @param array $rules
 *
 * @return \Cartalyst\Conditions\Condition
 */
function createCondition($name, $type, $target, array $actions = [], array $rules = [])
{
    $condition = new Condition(compact('name', 'type', 'target'));

    $condition->setActions($actions);

    $condition->setRules($rules);

    return $condition;
}

function getCouponsList()
{
    return [
        'PROMO14' => [
            'data' => [
                'code' => 'PROMO14',
                'name' => 'Limited Time 10% Off',
                'type' => 'coupon',
                'target' => 'subtotal',
            ],
            'actions' => [
                'value' => '-10%',
            ],
            'rules' => [],
        ],

        'DISC2014' => [
            'data' => [
                'code' => 'DISC2014',
                'name' => 'Limited Time $25 Off on all purchases over $200',
                'type' => 'coupon',
                'target' => 'subtotal',
            ],
            'actions' => [
                'value' => '-25',
            ],
            'rules' => new SerializableClosure(function () {
                return app('cart')->subtotal() > 200;
            }),
        ],
    ];
}
