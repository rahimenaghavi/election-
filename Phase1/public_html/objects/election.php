<?php
class election{
 
    // database connection and table name
    private $conn;
    private $table_name = "election";
 
    // object properties
    public $id;
    public $title;
    public $start_time;
    public $end_time;
    public $list_of_choices;
    public $number_of_votes;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    //**************************************************************************
    //**************************************************************************
    //**************************************************************************
    //          Get All Elections Function                
    //**************************************************************************
    //**************************************************************************
    //**************************************************************************

    // read elections
    function get_all_elections(){
 
        // select all query
        $query = " SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
 
        // prepare query statement
        $stmt = $this->conn->prepare($query);
 
        // execute query
        $stmt->execute();
 
        return $stmt;
    }


    //**************************************************************************
    //**************************************************************************
    //**************************************************************************
    //          Create An Election Function                
    //**************************************************************************
    //**************************************************************************
    //**************************************************************************
    
    // create election
    function create_election(){
 
        // query to insert record
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    title=:title, start_time=:start_time, end_time=:end_time, list_of_choices=:list_of_choices, number_of_votes=:number_of_votes";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->title=htmlspecialchars(strip_tags($this->title));
        $this->start_time=htmlspecialchars(strip_tags($this->start_time));
        $this->end_time=htmlspecialchars(strip_tags($this->end_time));
        $this->number_of_votes=htmlspecialchars(strip_tags($this->number_of_votes));

        //**************************
        // Save ListOfChioces To DataBase With This Format
        // Choice1-Choice2-Choice3
        $choices_str="";
        for($i=0 ; $i<sizeof($this->list_of_choices);$i++)
        {
            $choices_str=$choices_str."-".$this->list_of_choices[$i];
        }
        $choices_str = substr($choices_str, 1);
        //***************************
    
        // bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);
        $stmt->bindParam(":list_of_choices", $choices_str);
        $stmt->bindParam(":number_of_votes", $this->number_of_votes);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
     
    }


    //**************************************************************************
    //**************************************************************************
    //**************************************************************************
    //          Edit An Election Function                
    //**************************************************************************
    //**************************************************************************
    //**************************************************************************

    // edit the elec# -*- test-case-name: twisted.web.test.test_domhelpers -*-
# Copyright (c) Twisted Matrix Laboratories.
# See LICENSE for details.

"""
Specific tests for (some of) the methods in L{twisted.web.domhelpers}.
"""

from xml.dom import minidom

from twisted.trial.unittest import TestCase

from twisted.web import microdom

from twisted.web import domhelpers


class DOMHelpersTestsMixin:
    """
    A mixin for L{TestCase} subclasses which defines test methods for
    domhelpers functionality based on a DOM creation function provided by a
    subclass.
    """
    dom = None

    def test_getElementsByTagName(self):
        doc1 = self.dom.parseString('<foo/>')
        actual=domhelpers.getElementsByTagName(doc1, 'foo')[0].nodeName
        expected='foo'
        self.assertEqual(actual, expected)
        el1=doc1.documentElement
        actual=domhelpers.getElementsByTagName(el1, 'foo')[0].nodeName
        self.assertEqual(actual, expected)

        doc2_xml='<a><foo in="a"/><b><foo in="b"/></b><c><foo in="c"/></c><foo in="d"/><foo in="ef"/><g><foo in="g"/><h><foo in="h"/></h></g></a>'
        doc2 = self.dom.parseString(doc2_xml)
        tag_list=domhelpers.getElementsByTagName(doc2, 'foo')
        actual=''.join([node.getAttribute('in') for node in tag_list])
        expected='abcdefgh'
        self.assertEqual(actual, expected)
        el2=doc2.documentElement
        tag_list=domhelpers.getElementsByTagName(el2, 'foo')
        actual=''.join([node.getAttribute('in') for node in tag_list])
        self.assertEqual(actual, expected)

        doc3_xml='''
<a><foo in="a"/>
    <b><foo in="b"/>
        <d><foo in="d"/>
            <g><foo in="g"/></g>
            <h><foo in="h"/></h>
        </d>
        <e><foo in="e"/>
            <i><foo in="i"/></i>
        </e>
    </b>
    <c><foo in="c"/>
        <f><foo in="f"/>
            <j><foo in="j"/></j>
        </f>
    </c>
</a>'''
        doc3 = self.dom.parseString(doc3_xml)
        tag_list=domhelpers.getElementsByTagName(doc3, 'foo')
        actual=''.join([node.getAttribute('in') for node in tag_list])
        expected='abdgheicfj'
        self.assertEqual(actual, expected)
        el3=doc3.documentElement
        tag_list=domhelpers.getElementsByTagName(el3, 'foo')
        actual=''.join([node.getAttribute('in') for node in tag_list])
        self.assertEqual(actual, expected)

        doc4_xml='<foo><bar></bar><baz><foo/></baz></foo>'
        doc4 = self.dom.parseString(doc4_xml)
        actual=domhelpers.getElementsByTagName(doc4, 'foo')
        root=doc4.documentElement
        expected=[root, root.childNodes[-1].childNodes[0]]
        self.assertEqual(actual, expected)
        actual=domhelpers.getElementsByTagName(root, 'foo')
        self.assertEqual(actual, expected)


    def test_gatherTextNodes(self):
        doc1 = self.dom.parseString('<a>foo</a>')
        actual=domhelpers.gatherTextNodes(doc1)
        expected='foo'
        self.assertEqual(actual, expected)
        actual=domhelpers.gatherTextNodes(doc1.documentElement)
        self.assertEqual(actual, expected)

        doc2_xml='<a>a<b>b</b><c>c</c>def<g>g<h>h</h></g></a>'
        doc2 = self.dom.parseString(doc2_xml)
        actual=domhelpers.gatherTextNodes(doc2)
        expected='abcdefgh'
        self.assertEqual(actual, expected)
        actual=domhelpers.gatherTextNodes(doc2.documentElement)
        self.assertEqual(actual, expected)

        doc3_xml=('<a>a<b>b<d>d<g>g</g><h>h</h></d><e>e<i>i</i></e></b>' +
                  '<c>c<f>f<j>j</j></f></c></a>')
        doc3 = self.dom.parseString(doc3_xml)
        actual=domhelpers.gatherTextNodes(doc3)
        expected='abdgheicfj'
        self.assertEqual(actual, expected)
        actual=domhelpers.gatherTextNodes(doc3.documentElement)
        self.assertEqual(actual, expected)

    def test_clearNode(self):
        doc1 = self.dom.parseString('<a><b><c><d/></c></b></a>')
        a_node=doc1.documentElement
        domhelpers.clearNode(a_node)
        self.assertEqual(
            a_node.toxml(),
            self.dom.Element('a').toxml())

        doc2 = self.dom.parseString('<a><b><c><d/></c></b></a>')
        b_node=doc2.documentElement.childNodes[0]
        domhelpers.clearNode(b_node)
        actual=doc2.documentElement.toxml()
        expected = self.dom.Element('a')
        expected.appendChild(self.dom.Element('b'))
        self.assertEqual(actual, expected.toxml())


    def test_get(self):
        doc1 = self.dom.parseString('<a><b id="bar"/><c class="foo"/></a>')
        node=domhelpers.get(doc1, "foo")
        actual=node.toxml()
        expected = self.dom.Element('c')
        expected.setAttribute('class', 'foo')
        self.assertEqual(actual, expected.toxml())

        node=domhelpers.get(doc1, "bar")
        actual=node.toxml()
        expected = self.dom.Element('b')
        expected.setAttribute('id', 'bar')
        self.assertEqual(actual, expected.toxml())

        self.assertRaises(domhelpers.NodeLookupError, 
                          domhelpers.get, 
                          doc1, 
                          "pzork")

    def test_getIfExists(self):
        doc1 = self.dom.parseString('<a><b id="bar"/><c class="foo"/></a>')
        node=domhelpers.getIfExists(doc1, "foo")
        actual=node.toxml()
        expected = self.dom.Element('c')
        expected.setAttribute('class', 'foo')
        self.assertEqual(actual, expected.toxml())

        node=domhelpers.getIfExists(doc1, "pzork")
        self.assertIdentical(node, None)


    def test_getAndClear(self):
        doc1 = self.dom.parseString('<a><b id="foo"><c></c></b></a>')
        node=domhelpers.getAndClear(doc1, "foo")
        actual=node.toxml()
        expected = self.dom.Element('b')
        expected.setAttribute('id', 'foo')
        self.assertEqual(actual, expected.toxml())


    def test_locateNodes(self):
        doc1 = self.dom.parseString('<a><b foo="olive"><c foo="olive"/></b><d foo="poopy"/></a>')
        node_list=domhelpers.locateNodes(
            doc1.childNodes, 'foo', 'olive', noNesting=1)
        actual=''.join([node.toxml() for node in node_list])
        expected = self.dom.Element('b')
        expected.setAttribute('foo', 'olive')
        c = self.dom.Element('c')
        c.setAttribute('foo', 'olive')
        expected.appendChild(c)

        self.assertEqual(actual, expected.toxml())

        node_list=domhelpers.locateNodes(
            doc1.childNodes, 'foo', 'olive', noNesting=0)
        actual=''.join([node.toxml() for node in node_list])
        self.assertEqual(actual, expected.toxml() + c.toxml())


    def test_getParents(self):
        doc1 = self.dom.parseString('<a><b><c><d/></c><e/></b><f/></a>')
        node_list = domhelpers.getParents(
            doc1.childNodes[0].childNodes[0].childNodes[0])
        actual = ''.join([node.tagName for node in node_list
                          if hasattr(node, 'tagName')])
        self.assertEqual(actual, 'cba')


    def test_findElementsWithAttribute(self):
        doc1 = self.dom.parseString('<a foo="1"><b foo="2"/><c foo="1"/><d/></a>')
        node_list = domhelpers.findElementsWithAttribute(doc1, 'foo')
        actual = ''.join([node.tagName for node in node_list])
        self.assertEqual(actual, 'abc')

        node_list = domhelpers.findElementsWithAttribute(doc1, 'foo', '1')
        actual = ''.join([node.tagName for node in node_list])
        self.assertEqual(actual, 'ac')


    def test_findNodesNamed(self):
        doc1 = self.dom.parseString('<doc><foo/><bar/><foo>a</foo></doc>')
        node_list = domhelpers.findNodesNamed(doc1, 'foo')
        actual = len(node_list)
        self.assertEqual(actual, 2)

    # NOT SURE WHAT THESE ARE SUPPOSED TO DO..
    # def test_RawText  FIXME
    # def test_superSetAttribute FIXME
    # def test_superPrependAttribute FIXME
    # def test_superAppendAttribute FIXME
    # def test_substitute FIXME

    def test_escape(self):
        j='this string " contains many & characters> xml< won\'t like'
        expected='this string &quot; contains many &amp; characters&gt; xml&lt; won\'t like'
        s
