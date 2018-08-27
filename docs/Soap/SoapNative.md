# NFePHP\Common\Soap\SoapNative::class

Esta classe é responsável por realizar a comunicação com os webservices usnado o padrão SOAP da Receita Federal para o projeto SPED.

Esta classe usa o SOAP nativo do PHP. Porém o soap nativo não se adapta bem com as regras e estruturas estabelecidas pel SEFAZ, o traz uma outra série de problemas de solução mais complexa. 
Portanto é recomendável EVITE O USO DESTA CLASSE, ela existe aqui apenas para atender algumas necessidade do projeto sped-nfse.

## NÃO USE ESTA CLASSE para NFe, NFCe, CTe ou MDFe, prefira a SoapCurl mais rápida, ajustável e estável.