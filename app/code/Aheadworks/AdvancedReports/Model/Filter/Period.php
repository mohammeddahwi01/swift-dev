<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Filter;

use Aheadworks\AdvancedReports\Model\Source\Period as PeriodSource;
use Aheadworks\AdvancedReports\Model\Source\Compare as CompareSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Period
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Period
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_arep_period_type';

    /**
     * @var string
     */
    const SESSION_PERIOD_FROM_KEY = 'aw_arep_period_from';

    /**
     * @var string
     */
    const SESSION_PERIOD_TO_KEY = 'aw_arep_period_to';

    /**
     * @var string
     */
    const SESSION_IS_COMPARE_AVAILABLE_KEY = 'aw_arep_compare_available';

    /**
     * @var string
     */
    const SESSION_COMPARE_TYPE_KEY = 'aw_arep_compare_type';

    /**
     * @var string
     */
    const SESSION_COMPARE_FROM_KEY = 'aw_arep_compare_from';

    /**
     * @var string
     */
    const SESSION_COMPARE_TO_KEY = 'aw_arep_compare_to';

    /**
     * @var string
     */
    const PERIOD_TYPE_CUSTOM = 'custom';

    /**
     * @var string
     */
    const DEFAULT_PERIOD_TYPE = PeriodSource::TYPE_THIS_MONTH;

    /**
     * @var []
     */
    private $periodCache;

    /**
     * @var []
     */
    private $comparePeriodCache;

    /**
     * @var \DateTimeZone
     */
    private $localeTimezone;

    /**
     * @var PeriodSource
     */
    private $periodSource;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param PeriodSource $periodSource
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     * @param TimezoneInterface $localeDate
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        PeriodSource $periodSource,
        RequestInterface $request,
        SessionManagerInterface $session,
        TimezoneInterface $localeDate,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->periodSource = $periodSource;
        $this->request = $request;
        $this->session = $session;
        $this->localeDate = $localeDate;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve current period
     *
     * @return []
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getPeriod()
    {
        if (null == $this->periodCache) {
            if ($periodTypeFromRequest = $this->request->getParam('period_type')) {
                $periodType = $periodTypeFromRequest;
            } elseif ($periodTypeFromSession = $this->session->getData(self::SESSION_KEY)) {
                $periodType = $periodTypeFromSession;
            } else {
                $periodType = self::DEFAULT_PERIOD_TYPE;
            }

            switch ($periodType) {
                case self::PERIOD_TYPE_CUSTOM:
                    $from = $this->request->getParam('period_from');
                    $to = $this->request->getParam('period_to');
                    if (!$from || !$to) {
                        $from = $this->session->getData(self::SESSION_PERIOD_FROM_KEY);
                        $to = $this->session->getData(self::SESSION_PERIOD_TO_KEY);
                        if (!$from || !$to) {
                            $this->session->setData(self::SESSION_KEY, self::DEFAULT_PERIOD_TYPE);
                            return $this->getPeriod();
                        }
                    }
                    $this->session->setData(self::SESSION_PERIOD_FROM_KEY, $from);
                    $this->session->setData(self::SESSION_PERIOD_TO_KEY, $to);
                    try {
                        $from = new \DateTime($from, $this->getLocaleTimezone());
                        $to = new \DateTime($to, $this->getLocaleTimezone());
                    } catch (\Exception $e) {
                        // If not valid date
                        $this->request->setParams(
                            [
                                'period_from' => null,
                                'period_to', null,
                                'period_type' => self::DEFAULT_PERIOD_TYPE
                            ]
                        );
                        return $this->getPeriod();
                    }
                    break;
                default:
                    $range = $this->periodSource->getRangeList($this->getLocaleTimezone());
                    if (!array_key_exists($periodType, $range)) {
                        $this->_logger->critical(new \Exception('Unknown period type'));
                    }
                    $from = $range[$periodType]['from'];
                    $to = $range[$periodType]['to'];
            }
            $this->session->setData(self::SESSION_KEY, $periodType);
            $this->periodCache = [
                'type' => $periodType,
                'from' => $from,
                'to'   => $to,
            ];
        }
        return $this->periodCache;
    }

    /**
     * Retrieve period from
     *
     * @return \DateTime
     */
    public function getPeriodFrom()
    {
        $period = $this->getPeriod();
        return $period['from'];
    }

    /**
     * Retrieve period to
     *
     * @return \DateTime
     */
    public function getPeriodTo()
    {
        $period = $this->getPeriod();
        return $period['to'];
    }

    /**
     * Retrieve period type
     *
     * @return string
     */
    public function getPeriodType()
    {
        $period = $this->getPeriod();
        return $period['type'];
    }

    /**
     * Retrieve locale timezone
     *
     * @return \DateTimeZone
     */
    public function getLocaleTimezone()
    {
        if (!$this->localeTimezone) {
            $localeTimezone = $this->scopeConfig->getValue(
                $this->localeDate->getDefaultTimezonePath(),
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );
            $this->localeTimezone = new \DateTimeZone($localeTimezone);
        }
        return $this->localeTimezone;
    }

    /**
     * Retrieve current compare period
     *
     * @return []
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getComparePeriod()
    {
        if (null == $this->comparePeriodCache) {
            $comparePeriodType = $this->request->getParam('compare_type');
            $from = $this->request->getParam('compare_from');
            $to = $this->request->getParam('compare_to');
            $isAjaxRequest = $this->request->isAjax();

            if ((!$comparePeriodType || !$from || !$to) && $isAjaxRequest) {
                $comparePeriodType = $this->session->getData(self::SESSION_COMPARE_TYPE_KEY);
                $from = $this->session->getData(self::SESSION_COMPARE_FROM_KEY);
                $to = $this->session->getData(self::SESSION_COMPARE_TO_KEY);
            }
            $this->session->setData(self::SESSION_COMPARE_TYPE_KEY, $comparePeriodType);
            $this->session->setData(self::SESSION_COMPARE_FROM_KEY, $from);
            $this->session->setData(self::SESSION_COMPARE_TO_KEY, $to);

            $compareEnabled = false;
            if ($comparePeriodType && $from && $to) {
                $compareEnabled = true;
            }

            try {
                $from = new \DateTime($from, $this->getLocaleTimezone());
                $to = new \DateTime($to, $this->getLocaleTimezone());
            } catch (\Exception $e) {
                $comparePeriodType = CompareSource::TYPE_PREVIOUS_PERIOD;
                $from = null;
                $to = null;
                $compareEnabled = false;

            }
            $this->comparePeriodCache = [
                'enabled'       => $compareEnabled,
                'default_type'  => CompareSource::TYPE_PREVIOUS_PERIOD,
                'type'          => $comparePeriodType,
                'from'          => $from,
                'to'            => $to,
            ];

        }
        return $this->comparePeriodCache;
    }

    /**
     * Is compare enabled
     *
     * @return bool
     */
    public function isCompareEnabled()
    {
        $period = $this->getComparePeriod();
        return $period['enabled'];
    }

    /**
     * Retrieve compare from
     *
     * @return \DateTime
     */
    public function getCompareFrom()
    {
        $period = $this->getComparePeriod();
        return $period['from'];
    }

    /**
     * Retrieve compare to
     *
     * @return \DateTime
     */
    public function getCompareTo()
    {
        $period = $this->getComparePeriod();
        return $period['to'];
    }

    /**
     * Retrieve compare type
     *
     * @return string
     */
    public function getCompareType()
    {
        $period = $this->getComparePeriod();
        return $period['type'];
    }

    /**
     * Is compare available (should be set previously using setIsCompareAvailable)
     *
     * @return bool
     */
    public function isCompareAvailable()
    {
        return (bool)$this->session->getData(self::SESSION_IS_COMPARE_AVAILABLE_KEY);
    }

    /**
     * Set is compare available flag
     *
     * @param bool $isCompareAvailable
     * @return $this
     */
    public function setIsCompareAvailable($isCompareAvailable)
    {
        return $this->session->setData(self::SESSION_IS_COMPARE_AVAILABLE_KEY, $isCompareAvailable);
    }
}
