<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = null;
        if(isset($data['quote']) and $data['quote'] instanceof Quote)
        {
            $quote = $data['quote'];
        }

        if ($quote)
        {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);

            if(  strpos($text, '[quote:destination_link]') !== false){
                $destination = DestinationRepository::getInstance()->getById($quote->destinationId);
                $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destination->countryName . '/quote/' . $_quoteFromRepository->id, $text);
            }

            if (strpos($text, '[quote:summary_html]') != false) {
                $text = str_replace(
                    '[quote:summary_html]',
                    Quote::renderHtml($_quoteFromRepository),
                    $text
                );
            }
            if (strpos($text, '[quote:summary]') != false) {
                $text = str_replace(
                    '[quote:summary]',
                    Quote::renderText($_quoteFromRepository),
                    $text
                );
            }
            if(strpos($text, '[quote:destination_name]') !== false) 
            {
                $text = str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);
            }
        }
        $_user = $APPLICATION_CONTEXT->getCurrentUser();
        if(isset($data['user']) and ($data['user'] instanceof User) )
        {
            $_user = $data['user'];
        }
        if(strpos($text, '[user:first_name]') !== false)
        {
            $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
}
