<?php
namespace Vendor\ModuleName\Model;

use Magento\Framework\App\ResourceConnection;

class CustomQuery
{
    protected $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function execute()
    {
        $connection = $this->resource->getConnection();

        $queries = [
            "SET @rownum := 0;",
            "SELECT FLOOR((@rownum := @rownum + 1 - 1) / 5) + 1 AS `num`, SUM(`count`) AS `count`
            FROM (
                SELECT `main_table`.*, COUNT(attach.review_id) AS `count`
                FROM `aw_ar_review` AS `main_table`
                LEFT JOIN `aw_ar_review_shared_store` ON main_table.id = aw_ar_review_shared_store.review_id
                LEFT JOIN `aw_ar_review_attachment` AS `attach` ON main_table.id = attach.review_id
                WHERE `main_table`.`created_at` <= '2024-07-12 06:48:14'
                  AND `main_table`.`status` = '1'
                  AND `is_featured` = '1'
                  AND (`main_table`.`store_id` = '1' OR `aw_ar_review_shared_store`.`store_id` = '1')
                GROUP BY `main_table`.`id`
                ORDER BY `votes_positive` DESC, `main_table`.`created_at` DESC
            ) AS `t`
            GROUP BY `num`;"
        ];

        foreach ($queries as $query) {
            $connection->query($query);
        }
    }
}
