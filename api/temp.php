<?php
// Contenu du WSDL corrigé
$wsdl = '<?xml version="1.0" encoding="UTF-8"?>
<definitions name="UserService" 
            targetNamespace="http://localhost/dic2_news/api/soap_users.php" 
            xmlns="http://schemas.xmlsoap.org/wsdl/" 
            xmlns:tns="http://localhost/dic2_news/api/soap_users.php" 
            xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" 
            xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
            xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" 
            xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" 
            xmlns:soap12="http://schemas.xmlsoap.org/wsdl/soap12/">
    <types>
        <xsd:schema targetNamespace="http://localhost/dic2_news/api/soap_users.php">
            <xsd:complexType name="User">
                <xsd:all>
                    <xsd:element name="id" type="xsd:int"/>
                    <xsd:element name="username" type="xsd:string"/>
                    <xsd:element name="email" type="xsd:string"/>
                    <xsd:element name="nom" type="xsd:string"/>
                    <xsd:element name="prenom" type="xsd:string"/>
                    <xsd:element name="role" type="xsd:string"/>
                    <xsd:element name="dateCreation" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="dateModification" type="xsd:string" minOccurs="0"/>
                    <xsd:element name="actif" type="xsd:boolean" minOccurs="0"/>
                    <xsd:element name="derniereConnexion" type="xsd:string" minOccurs="0"/>
                </xsd:all>
            </xsd:complexType>
            <xsd:complexType name="ArrayOfUsers">
                <xsd:complexContent>
                    <xsd:restriction base="soapenc:Array">
                        <xsd:attribute ref="soapenc:arrayType" wsdl:arrayType="tns:User[]"/>
                    </xsd:restriction>
                </xsd:complexContent>
            </xsd:complexType>
        </xsd:schema>
    </types>
    
    <message name="listUsersRequest">
        <part name="token" type="xsd:string"/>
    </message>
    <message name="listUsersResponse">
        <part name="return" type="tns:ArrayOfUsers"/>
    </message>
    
    <message name="getUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="userId" type="xsd:int"/>
    </message>
    <message name="getUserResponse">
        <part name="return" type="tns:User"/>
    </message>
    
    <message name="createUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="username" type="xsd:string"/>
        <part name="email" type="xsd:string"/>
        <part name="password" type="xsd:string"/>
        <part name="nom" type="xsd:string"/>
        <part name="prenom" type="xsd:string"/>
        <part name="role" type="xsd:string"/>
    </message>
    <message name="createUserResponse">
        <part name="return" type="xsd:int"/>
    </message>
    
    <message name="updateUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="userId" type="xsd:int"/>
        <part name="username" type="xsd:string"/>
        <part name="email" type="xsd:string"/>
        <part name="nom" type="xsd:string"/>
        <part name="prenom" type="xsd:string"/>
        <part name="role" type="xsd:string"/>
        <part name="password" type="xsd:string"/>
    </message>
    <message name="updateUserResponse">
        <part name="return" type="xsd:boolean"/>
    </message>
    
    <message name="deleteUserRequest">
        <part name="token" type="xsd:string"/>
        <part name="userId" type="xsd:int"/>
    </message>
    <message name="deleteUserResponse">
        <part name="return" type="xsd:boolean"/>
    </message>
    
    <message name="authenticateRequest">
        <part name="username" type="xsd:string"/>
        <part name="password" type="xsd:string"/>
    </message>
    <message name="authenticateResponse">
        <part name="return" type="tns:User"/>
    </message>
    
    <portType name="UserServicePortType">
        <operation name="listUsers">
            <input message="tns:listUsersRequest"/>
            <output message="tns:listUsersResponse"/>
        </operation>
        <operation name="getUser">
            <input message="tns:getUserRequest"/>
            <output message="tns:getUserResponse"/>
        </operation>
        <operation name="createUser">
            <input message="tns:createUserRequest"/>
            <output message="tns:createUserResponse"/>
        </operation>
        <operation name="updateUser">
            <input message="tns:updateUserRequest"/>
            <output message="tns:updateUserResponse"/>
        </operation>
        <operation name="deleteUser">
            <input message="tns:deleteUserRequest"/>
            <output message="tns:deleteUserResponse"/>
        </operation>
        <operation name="authenticate">
            <input message="tns:authenticateRequest"/>
            <output message="tns:authenticateResponse"/>
        </operation>
    </portType>
    
    <binding name="UserServiceBinding" type="tns:UserServicePortType">
        <soap12:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
        <operation name="listUsers">
            <soap12:operation soapAction="http://localhost/dic2_news/api/soap_users.php#listUsers"/>
            <input><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="getUser">
            <soap12:operation soapAction="http://localhost/dic2_news/api/soap_users.php#getUser"/>
            <input><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="createUser">
            <soap12:operation soapAction="http://localhost/dic2_news/api/soap_users.php#createUser"/>
            <input><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="updateUser">
            <soap12:operation soapAction="http://localhost/dic2_news/api/soap_users.php#updateUser"/>
            <input><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="deleteUser">
            <soap12:operation soapAction="http://localhost/dic2_news/api/soap_users.php#deleteUser"/>
            <input><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
        <operation name="authenticate">
            <soap12:operation soapAction="http://localhost/dic2_news/api/soap_users.php#authenticate"/>
            <input><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
            <output><soap12:body use="encoded" namespace="http://localhost/dic2_news/api/soap_users.php" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
        </operation>
    </binding>
    
    <service name="UserServiceService">
        <port name="UserServicePort" binding="tns:UserServiceBinding">
            <soap12:address location="http://localhost/dic2_news/api/soap_users.php"/>
        </port>
    </service>
</definitions>';

// Afficher le WSDL
header('Content-Type: text/xml; charset=utf-8');
echo $wsdl;
?> 