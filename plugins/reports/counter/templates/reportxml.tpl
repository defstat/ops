{**
 * plugins/reports/counter/templates/reportxml.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * COUNTER report XML
 *}
{if $omitWrapper neq 'true'}
<?xml version="1.0" encoding="UTF-8"?>
<Reports xmlns="http://www.niso.org/schemas/counter"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.niso.org/schemas/counter http://www.niso.org/schemas/sushi/counter3_0.xsd">
{/if}
  <Report xmlns="http://www.niso.org/schemas/counter" ID="JR1" Version="3" Title="Server Report 1" Created="{$smarty.now|date_format:"%Y-%m-%dT%H:%M:%SZ"}">
    <Vendor>
      <Name>{$vendorName|escape:"html"}</Name>
      <ID>ID0</ID>
      <WebSiteUrl>{$base_url|escape:"html"}</WebSiteUrl>
    </Vendor>
    <Customer>
      <Name>{$reqUserName|escape:"html"}</Name>
      <ID>{$reqUserId|escape:"html"}</ID>

      {foreach from=$serversArray key=serverkey item=server}

      <ReportItems>
        <ItemIdentifier>
          <Type>Print_ISSN</Type>
          <Value>{$server.printIssn|escape:"html"}</Value>
        </ItemIdentifier>
        <ItemIdentifier>
          <Type>Online_ISSN</Type>
          <Value>{$server.onlineIssn|escape:"html"}</Value>
        </ItemIdentifier>
        <ItemPlatform>Open Preprint Systems</ItemPlatform>
        <ItemPublisher>{$server.publisherInstitution|escape:"html"}</ItemPublisher>
        <ItemName>{$server.serverTitle|escape:"html"}</ItemName>
        <ItemDataType>Server</ItemDataType>

        {foreach from=$server.entries key=key item=requests}

        <ItemPerformance>
          <Period>
            <Begin>{$requests.start}</Begin>
            <End>{$requests.end}</End>
          </Period>
          <Category>Requests</Category>
          {if $requests.count_total neq ''}<Instance>
            <MetricType>ft_total</MetricType>
            <Count>{$requests.count_total}</Count>
          </Instance>{/if}

          {if $requests.count_html neq ''}<Instance>
            <MetricType>ft_html</MetricType>
            <Count>{$requests.count_html}</Count>
          </Instance>{/if}

          {if $requests.count_pdf neq ''}<Instance>
            <MetricType>ft_pdf</MetricType>
            <Count>{$requests.count_pdf}</Count>
          </Instance>{/if}

        </ItemPerformance>

        {/foreach}

      </ReportItems>

      {/foreach}

    </Customer>
  </Report>
{if $omitWrapper neq 'true'}
</Reports>
{/if}
