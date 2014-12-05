<?php

namespace AllenJB\PafUtils;

/**
 * Note: Some behaviours / combinations tested here (particularly those where both building name and sub-building name
 * are covered by the 'exception' rules are not actually defined in the Programmers Guide, but have been included here
 * for completeness and to define what the library will do should the situation ever arise.
 *
 * It's possible that if these situations ever arise, the actual result Royal Mail expects is different from what has
 * been predicted here, in which case a bug should be filed against the library.
 *
 * @todo Tests with randomly generated data
 * @todo Tests for split building name rules
 * @todo Tests based on presence of (Dependent) Thoroughfare and (Double) Dependent Locality fields
 */
class AddressAssembleTest extends \PHPUnit_Framework_TestCase
{

    protected $buildingNameNoException = 'Test Building Name';

    /**
     * @var string First and last characters of the Building Name are numeric (eg ‘1to1’ or ’100:1’)
     */
    protected $buildingNameException1 = '12-34';

    /**
     * @var string First and penultimate characters are numeric, last character is alphabetic (eg 12A’)
     */
    protected $buildingNameException2 = '123A';

    /**
     * @var string Building Name has only one character (eg ‘A’)
     */
    protected $buildingNameException3 = 'Z';

    /**
     * @var string Has a numeric range or a numeric alpaha suffix, and is prefixed by specified keywords
     */
    protected $buildingNameException4 = 'Unit 1-2';

    /**
     * @var string Text followed by a space, then by numerics/numeric ranges with the numeric part an exception
     */
    protected $buildingNameSplit = 'Test House 1024A';

    /**
     * @var string Building number part from buildingNameSplit above
     */
    protected $buildingNameSplitNumber = '1024A';

    /**
     * @var string Building name part from buildingNameSplit above
     */
    protected $buildingNameSplitName = 'Test House';

    protected $subBuildingNameNoException = 'Test Sub-Building Name';

    /**
     * @var string First and last characters of the Building Name are numeric (eg ‘1to1’ or ’100:1’)
     */
    protected $subBuildingNameException1 = '56-78';

    /**
     * @var string First and penultimate characters are numeric, last character is alphabetic (eg 12A’)
     */
    protected $subBuildingNameException2 = '456B';

    /**
     * @var string Building Name has only one character (eg ‘A’)
     */
    protected $subBuildingNameException3 = 'Y';

    /**
     * @var string Has a numeric range or a numeric alpaha suffix, and is prefixed by specified keywords
     */
    protected $subBuildingNameException4 = 'Rear of 5A';


    public function testRule1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setOrganizationName('Test Organization');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 1);
    }


    public function testRule2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setOrganizationName('Test Organization');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 2);
    }


    public function testRule3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameNoException)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameNoException,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 3);
    }


    public function testRule3Split()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameSplit)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameSplitName,
            $this->buildingNameSplitNumber .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        // Actually gets processed by Rule 4 because we perform the split before evaluating rules
        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 4);
    }


    public function testRule3Exception1()
        {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException1)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameException1 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 3);
    }


    public function testRule3Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameException2 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 3);
    }


    public function testRule3Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException3)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameException3 .', Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 3);
    }


    public function testRule3Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setDependentThoroughfare('Test Industrial Estate')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException4)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameException4,
            'Test Industrial Estate',
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 3);
    }


    public function testRule4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameNoException)
            ->setOrganizationName('Test Organization')
            ->setDepartmentName('Test Department');
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            'Test Organization',
            'Test Department',
            $this->buildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 4);
    }


    public function testRule5()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 5);
    }


    public function testRule6()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameNoException,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_NoException_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameNoException,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_NoException_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameNoException,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_NoException_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameNoException,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_NoException_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameNoException,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception1_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException1 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception1_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException1 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception1_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException1 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception1_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException1  .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception1_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException1 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception2_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException2 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception2_Exception1()
    {
        $address = new Address();
        $address->setUdprn(7)
            ->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException2 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception2_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException2 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception2_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException2 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception2_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException2 .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception3_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException3 .', Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception3_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException3 .', Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception3_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException3 .', Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception3_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException3 .', Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception3_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException3 .', Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception4_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException4,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception4_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException4,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception4_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException4,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception4_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException4,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule6_Exception4_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException4,
            'Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 6);
    }


    public function testRule7()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_NoException_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_NoException_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_NoException_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_NoException_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameNoException)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameNoException,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception1_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException1,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception1_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException1,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception1_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException1,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception1_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException1,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception1_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException1)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException1,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception2_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException2,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception2_Exception1()
    {
        $address = new Address();
        $address->setUdprn(7)
            ->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setBuildingNumber(123)
            ->setThoroughfare('Test Street')
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException2,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception2_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException2,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception2_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException2,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception2_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException2)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException2,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception3_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException3,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception3_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException3,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception3_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException3,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception3_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException3,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception3_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException3)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException3,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception4_NoException()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException,
            $this->buildingNameException4,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception4_Exception1()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException1);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException1 .' '. $this->buildingNameException4,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception4_Exception2()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException2);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException2 .' '. $this->buildingNameException4,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception4_Exception3()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException3);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException3 .', '. $this->buildingNameException4,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRule7_Exception4_Exception4()
    {
        $address = new Address();
        $address->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setBuildingNumber(123)
            ->setBuildingName($this->buildingNameException4)
            ->setSubBuildingName($this->subBuildingNameException4);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameException4,
            $this->buildingNameException4,
            '123 Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 7);
    }


    public function testRuleC1()
    {
        $address = new Address();
        $address->setUdprn(573)
            ->setPostCode('AB12 3CD')
            ->setPostTown('Test Town')
            ->setThoroughfare('Test Street')
            ->setSubBuildingName($this->subBuildingNameNoException);
        $addressLines = $address->getAddressLines();

        $correctAddressLines = array(
            $this->subBuildingNameNoException .' Test Street',
        );

        $this->assertEquals($correctAddressLines, $addressLines);

        $debugFlags = $address->getAssemblyDebugFlags();
        $this->assertEquals($debugFlags['rule'], 'c1');
    }
}
