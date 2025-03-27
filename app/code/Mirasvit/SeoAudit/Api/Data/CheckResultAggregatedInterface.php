<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoAudit\Api\Data;

interface CheckResultAggregatedInterface
{
    const TABLE_NAME = 'mst_seo_audit_check_result_aggregated';
    const ID = 'aggregated_id';
    const JOB_ID = 'job_id';
    const IDENTIFIER = 'identifier';
    const TOTAL = 'total';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';

    public function getId(): int;

    public function getJobId(): int;

    public function setJobId(int $jobId): self;

    public function getIdentifier(): string;

    public function setIdentifier(string $identifier): self;

    public function getTotal(): int;

    public function setTotal(int $total): self;

    public function getError(): int;

    public function setError(int $error): self;

    public function getWarning(): int;

    public function setWarning(int $warning): self;

    public function getNotice(): int;

    public function setNotice(int $notice): self;
}
