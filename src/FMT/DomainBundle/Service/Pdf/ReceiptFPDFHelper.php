<?php


namespace FMT\DomainBundle\Service\Pdf;

use FMT\DomainBundle\Service\ReceiptHelperInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ReceiptFPDFHelper
 * @package FMT\DomainBundle\Service\Pdf
 */
class ReceiptFPDFHelper extends \FPDF implements ReceiptHelperInterface
{
    const HEIGHT_DEFAULT = 10;
    const HEIGHT_TOP_BLOCK = 5;
    const HEIGHT_TABLE_BLOCK = 10;

    const WIDTH_TOP_BLOCK = 90;

    const HEADER_WIDTH = [
        130,
        0,
        0,
    ];

    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $projectDir;

    /** @var ReceiptDto */
    protected $info;

    /** @var ReceiptItemDto[]|array */
    protected $items;

    /** @var string */
    protected $name;

    /**
     * @param TranslatorInterface $translator
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param $projectDir
     */
    public function __construct($projectDir)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
    }


    /**
     * @param ReceiptDto $info
     * @param ReceiptItemDto[]|array $items
     * @return string
     */
    public function getReceipt(ReceiptDto $info, array $items)
    {
        $this->info = $info;
        $this->items = $items;

        $arr = ['Receipt', $this->info->number];
        $arr = array_filter($arr);
        $this->name = implode('-', $arr);

        $this
            ->setBase()
            ->addLogo()
            ->addGeneralInfo()
            ->addTable()
            ->addFooter()
        ;

        return $this->Output('D', sprintf("%s.pdf", $this->name));
    }


    /**
     * @return $this
     */
    protected function setBase()
    {
        $this->SetTitle($this->name);

        $this->SetFont(self::FONT_FAMILY_DEFAULT, '', self::FONT_SIZE_DEFAULT);
        $this->SetTextColor(50, 60, 100);

        $this->SetMargins(20, 20);
        $this->AddPage();

        return $this;
    }

    /**
     * @return $this
     */
    protected function addLogo()
    {
        $imagePath = sprintf('%s/src/FMT/PublicBundle/Resources/public/images/logo.svg', $this->projectDir);
        $image = $this->Image($imagePath, $this->GetX(), $this->GetY() + 7, 60);
        $this->Cell(0, 0, $image);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addGeneralInfo()
    {
        $this->SetFontSize(self::FONT_SIZE_TITLE);
        $text = $this->translator->trans('fmt.transaction_history.receipt.general.receipt');
        $this->Cell(0, self::HEIGHT_DEFAULT + 10, $text, 0, 1, 'R');
        $this->SetFontSize(self::FONT_SIZE_DEFAULT);

        $width = $this->GetPageWidth();

        if ($this->info->number) {
            $this->SetX($width - self::WIDTH_TOP_BLOCK);
            $text = $this->translator->trans('fmt.transaction_history.receipt.general.receipt_number');
            $this->Cell(0, self::HEIGHT_TOP_BLOCK, $text);
            $this->Cell(0, self::HEIGHT_TOP_BLOCK, $this->info->number, 0, 1, 'R');
        }

        $this->SetX($width - self::WIDTH_TOP_BLOCK);
        $text = $this->translator->trans('fmt.transaction_history.receipt.general.date_paid');
        $this->Cell(0, self::HEIGHT_TOP_BLOCK, $text);
        $this->Cell(0, self::HEIGHT_TOP_BLOCK, $this->info->date->format('M j, Y'), 0, 1, 'R');

        if ($this->info->paymentMethod) {
            $this->SetX($width - self::WIDTH_TOP_BLOCK);
            $text = $this->translator->trans('fmt.transaction_history.receipt.general.payment_method');
            $this->Cell(0, self::HEIGHT_TOP_BLOCK, $text);
            $this->Cell(0, self::HEIGHT_TOP_BLOCK, $this->info->paymentMethod, 0, 1, 'R');
        }

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();

        $this->SetFontSize(self::FONT_SIZE_TITLE);
        $text = $this->translator->trans('fmt.transaction_history.receipt.general.description', [
            '%amount%' => $this->info->amount,
            '%date%' => $this->info->date->format('M j, Y'),
        ]);
        $this->Cell(0, self::HEIGHT_TOP_BLOCK, $text, 0, 1);
        $this->SetFontSize(self::FONT_SIZE_DEFAULT);

        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();

        return $this;
    }

    /**
     * @return $this
     */
    protected function addTable()
    {
        $this->SetFontSize(self::FONT_SIZE_TABLE_BLOCK);

        $template = 'fmt.transaction_history.receipt.table.header.';
        $len = count(self::HEADER_TITLES);
        for ($i = 0; $i < $len; $i++) {
            $align = $i == $len - 1 ? 'R' : 'L';
            $this->Cell(
                self::HEADER_WIDTH[$i],
                self::HEIGHT_TABLE_BLOCK,
                $this->translator->trans($template . self::HEADER_TITLES[$i]),
                'B',
                0,
                $align
            );
        }
        $this->Ln();

        foreach ($this->items as $item) {
            $this->Cell(self::HEADER_WIDTH[0], self::HEIGHT_TABLE_BLOCK, $item->description);
            $this->Cell(self::HEADER_WIDTH[1], self::HEIGHT_TABLE_BLOCK, $item->qty);
            $this->Cell(self::HEADER_WIDTH[2], self::HEIGHT_TABLE_BLOCK, $item->amount, 0, 0, 'R');
            $this->Ln();
        }

        $this->SetFont(self::FONT_FAMILY_DEFAULT, 'B');

        $width = array_sum(self::HEADER_WIDTH) - self::HEADER_WIDTH[2];
        $text = $this->translator->trans('fmt.transaction_history.receipt.table.total');
        $this->Cell($width, self::HEIGHT_TABLE_BLOCK, $text, 'T');
        $this->Cell(self::HEADER_WIDTH[2], self::HEIGHT_TABLE_BLOCK, $this->info->amount, 'T', 1, 'R');

        $this->SetFont(self::FONT_FAMILY_DEFAULT, '', self::FONT_SIZE_DEFAULT);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addFooter()
    {
        $this->SetY(-35);
        $this->SetFont(self::FONT_FAMILY_DEFAULT, '', self::FONT_SIZE_FOOTER);

        $arr = [$this->info->number, $this->translator->trans('fmt.transaction_history.receipt.footer')];
        $arr = array_filter($arr);
        $text = implode(' - ', $arr);
        $this->Cell(0, self::HEIGHT_DEFAULT, $text, 'T', 0, 'R');

        return $this;
    }
}
