<?php 
$exa   = '#eaffdb';
$band  = false;

?>
<page backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm">
	<page_header>
        <table style="width: 100%;">
            <tr>
                <td style="width: 100%;text-align:left;">
                    {{ asset:image file="pdf/cintillo_header.png" style="width:100%;" }}
                    
                </td>
                
                
            </tr>
            
        </table>
    </page_header>
    <page_footer>
        <table style="width: 100%; ">
            <tr>
                <td style="text-align: left;    width: 100%">{{ theme:image file="pdf/cintillo_footer.png" style="width:100%;" }}</td>
                
            </tr>
        </table>
    </page_footer>
    <h4 style="text-align: center;">DEPOSITOS REALIZADOS({{mes}} {{anio}})</h4>
    
    <h4>RESUMEN</h4>
    <table  style="width: 100%;border:#000 1px solid;font-size: 12px;">
        <tbody>
            <tr>
                <td style="width: 25%;">CENTRO/PLANTEL:</td>
                <td style="width: 50%;text-transform: uppercase;">{{ centro }}</td>
            </tr>
            <tr>
                <td style="width: 25%;">MES:</td>
                <td style="width: 50%;">{{ mes }}</td>
            </tr>
            <tr>
                <td style="width: 25%;">CANTIDAD DE DEPOSITOS:</td>
                <td style="width: 50%;"><?=count($depositos)?></td>
            </tr>
            <tr>
                <td style="width: 25%;">IMPORTE TOTAL:</td>
                <td style="width: 50%;"><?=number_format($total,2,'.',',')?></td>
            </tr>
        </tbody>
    </table>
    <br />
    <h4>DESGLOSE</h4>
    <table style="width: 100%;padding:4px;">
        <thead>
            <tr>
                <th style="width: 8%; background:#CCFF99;padding:4px;text-align:center;">#</th>
                <th style="width: 42%;background:#CCFF99;padding:4px;">CONCEPTO</th>
                <th style="width: 15%;background:#CCFF99;padding:4px;">TIPO</th>
                <th style="width: 15%;background:#CCFF99;padding:4px;">FECHA</th>
                <th style="width: 20%;background:#CCFF99;padding:4px;text-align:center;">IMPORTE</th> 
                  
            </tr>
        </thead>
        <tbody>
        
        <?php foreach($depositos as $index=>$deposito):?>
              
              
            <tr style="background:<?=$band?$exa:'#FFF'?>;">
                <td style="padding:3px;width: 8%;text-align:center;"><?=$index+1?></td>
                <td style="padding:3px;width: 42%;text-transform: uppercase;">
                   <?=$deposito->concepto;?>
                </td>
                <td style="padding:3px;width: 15%;text-transform: uppercase;"><?=ucfirst($deposito->tipo)?> </td>
                <td style="padding:3px;width: 15%;"><?=format_date_calendar($deposito->fecha_deposito)?> </td>
                <td style="padding:3px;width: 20%;text-align:right;"><?=number_format($deposito->importe,2,'.',',')?></td>
                <?php $total += $deposito->importe; ?>
            </tr>
            <?php $band = !$band?>
        <?php endforeach;?>
        </tbody>
        
    </table>
   
    
</page>