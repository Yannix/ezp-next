<?php
namespace eZ\Publish\Core\Repository;

use eZ\Publish\API\Repository\IOService as IOServiceInterface,
    eZ\Publish\API\Repository\Repository as RepositoryInterface,
    eZ\Publish\SPI\IO\Handler,

    eZ\Publish\API\Repository\Values\IO\BinaryFile,
    eZ\Publish\API\Repository\Values\IO\ContentType,
    eZ\Publish\API\Repository\Values\IO\BinaryFileCreateStruct,

    eZ\Publish\SPI\IO\BinaryFile as SPIBinaryFile,
    eZ\Publish\SPI\IO\BinaryFileCreateStruct as SPIBinaryFileCreateStruct,

    eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue,
    eZ\Publish\API\Repository\Exceptions\NotFoundException,
    eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;


/**
 * The io service for managing binary files
 *
 * @package eZ\Publish\Core\Repository
 *
 */
class IOService implements IOServiceInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    protected $repository;

    /**
     * @var \eZ\Publish\SPI\IO\Handler
     */
    protected $ioHandler;

    /**
     * @var array
     */
    protected $settings;

    /**
     * Setups service with reference to repository object that created it & corresponding handler
     *
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\SPI\IO\Handler $handler
     * @param array $settings
     */
    public function __construct( RepositoryInterface $repository, Handler $handler, array $settings = array() )
    {
        $this->repository = $repository;
        $this->ioHandler = $handler;
        $this->settings = $settings;
    }

    /**
     * Creates a BinaryFileCreateStruct object from the uploaded file $uploadedFile
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException When given an invalid uploaded file
     *
     * @param array $uploadedFile The $_POST hash of an uploaded file
     *
     * @return \eZ\Publish\API\Repository\Values\IO\BinaryFileCreateStruct
     */
    public function newBinaryCreateStructFromUploadedFile( array $uploadedFile )
    {
        $ioHandler = $this->ioHandler;

        if ( empty( $uploadedFile['name'] ) || !is_string( $uploadedFile['name'] ) )
            throw new InvalidArgumentException( "uploadedFile", "uploadedFile['name'] does not exist or has invalid value" );

        if ( empty( $uploadedFile['type'] ) || !is_string( $uploadedFile['type'] ) )
            throw new InvalidArgumentException( "uploadedFile", "uploadedFile['type'] does not exist or has invalid value" );

        if ( empty( $uploadedFile['tmp_name'] ) || !is_string( $uploadedFile['tmp_name'] ) )
            throw new InvalidArgumentException( "uploadedFile", "uploadedFile['tmp_name'] does not exist or has invalid value" );

        if ( empty( $uploadedFile['size'] ) || !is_int( $uploadedFile['size'] ) || $uploadedFile['size'] < 0 )
            throw new InvalidArgumentException( "uploadedFile", "uploadedFile['size'] does not exist or has invalid value" );

        if ( isset( $uploadedFile['error'] ) && $uploadedFile['error'] !== 0 )
            throw new InvalidArgumentException( "uploadedFile", "file was not uploaded correctly" );

        if ( !$ioHandler->exists( $uploadedFile['tmp_name'] ) )
            throw new InvalidArgumentException( "uploadedFile", "file was not uploaded correctly" );

        $fileHandle = $ioHandler->getFileResource( $uploadedFile['tmp_name'] );
        if ( $fileHandle === false )
            throw new InvalidArgumentException( "uploadedFile", "failed to get file resource" );

        $binaryCreateStruct = new BinaryFileCreateStruct();
        $binaryCreateStruct->contentType = new ContentType( $uploadedFile['type'] );
        $binaryCreateStruct->uri = $uploadedFile['tmp_name'];
        $binaryCreateStruct->originalFileName = $uploadedFile['name'];
        $binaryCreateStruct->size = $uploadedFile['size'];
        $binaryCreateStruct->inputStream = $fileHandle;

        return $binaryCreateStruct;
    }

    /**
     * Creates a BinaryFileCreateStruct object from $localFile
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException When given a non existing / unreadable file
     *
     * @param string $localFile Path to local file
     *
     * @return \eZ\Publish\API\Repository\Values\IO\BinaryFileCreateStruct
     */
    public function newBinaryCreateStructFromLocalFile( $localFile )
    {
        $ioHandler = $this->ioHandler;

        try
        {
            $binaryFile = $ioHandler->load( $localFile );
        }
        catch ( NotFoundException $e )
        {
            throw new InvalidArgumentException( "localFile", "file does not exist" );
        }

        $fileHandle = $ioHandler->getFileResource( $localFile );
        if ( $fileHandle === false )
            throw new InvalidArgumentException( "localFile", "failed to get file resource" );

        $binaryCreateStruct = new BinaryFileCreateStruct();
        $binaryCreateStruct->contentType = new ContentType( $binaryFile->mimeType );
        $binaryCreateStruct->uri = $binaryFile->uri;
        $binaryCreateStruct->originalFileName = $binaryFile->originalFile;
        $binaryCreateStruct->size = $binaryFile->size;
        $binaryCreateStruct->inputStream = $fileHandle;

        return $binaryCreateStruct;
    }

    /**
     * Creates a  binary file in the the repository
     *
     * @param \eZ\Publish\API\Repository\Values\IO\BinaryFileCreateStruct $binaryFileCreateStruct
     *
     * @return \eZ\Publish\API\Repository\Values\IO\BinaryFile The created BinaryFile object
     */
    public function createBinaryFile( BinaryFileCreateStruct $binaryFileCreateStruct )
    {
        if ( !$binaryFileCreateStruct->contentType instanceof ContentType )
            throw new InvalidArgumentValue( "contentType", "invalid content type", "BinaryFileCreateStruct" );

        if ( empty( $binaryFileCreateStruct->uri ) || !is_string( $binaryFileCreateStruct->uri ) )
            throw new InvalidArgumentValue( "uri", $binaryFileCreateStruct->uri, "BinaryFileCreateStruct" );

        if ( empty( $binaryFileCreateStruct->originalFileName ) || !is_string( $binaryFileCreateStruct->originalFileName ) )
            throw new InvalidArgumentValue( "originalFileName", $binaryFileCreateStruct->originalFileName, "BinaryFileCreateStruct" );

        if ( !is_int( $binaryFileCreateStruct->size ) || $binaryFileCreateStruct->size < 0 )
            throw new InvalidArgumentValue( "size", $binaryFileCreateStruct->size, "BinaryFileCreateStruct" );

        if ( !is_resource( $binaryFileCreateStruct->inputStream ) )
            throw new InvalidArgumentValue( "inputStream", "property is not a file resource", "BinaryFileCreateStruct" );

        $spiBinaryCreateStruct = $this->buildSPIBinaryFileCreateStructObject( $binaryFileCreateStruct );

        $spiBinaryFile = $this->ioHandler->create( $spiBinaryCreateStruct );

        return $this->buildDomainBinaryFileObject( $spiBinaryFile );
    }

    /**
     * Deletes the BinaryFile with $path
     *
     * @param \eZ\Publish\API\Repository\Values\IO\BinaryFile $binaryFile
     */
    public function deleteBinaryFile( BinaryFile $binaryFile )
    {
        //@todo: is $binaryFile->id equal to file path?
        if ( empty( $binaryFile->id ) || !is_string( $binaryFile->id ) )
            throw new InvalidArgumentValue( "id", $binaryFile->id, "BinaryFile" );

        $this->ioHandler->delete( $binaryFile->id );
    }

    /**
     * Loads the binary file with $id
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @param string $binaryFileid
     *
     * @return \eZ\Publish\API\Repository\Values\IO\BinaryFile
     */
    public function loadBinaryFile( $binaryFileid )
    {
        if ( empty( $binaryFileid ) || !is_string( $binaryFileid ) )
            throw new InvalidArgumentValue( "binaryFileid", $binaryFileid );

        //@todo: is binaryFileid equal to path?
        $spiBinaryFile = $this->ioHandler->load( $binaryFileid );

        return $this->buildDomainBinaryFileObject( $spiBinaryFile );
    }

    /**
     * Returns a read (mode: rb) file resource to the binary file identified by $path
     *
     * @param \eZ\Publish\API\Repository\Values\IO\BinaryFile $binaryFile
     *
     * @return resource
     */
    public function getFileInputStream( BinaryFile $binaryFile )
    {
        if ( empty( $binaryFile->uri ) || !is_string( $binaryFile->uri ) )
            throw new InvalidArgumentValue( "uri", $binaryFile->uri, "BinaryFile" );

        return $this->ioHandler->getFileResource( $binaryFile->uri );
    }

    /**
     * Returns the content of the binary file
     *
     * @param \eZ\Publish\API\Repository\Values\IO\BinaryFile $binaryFile
     *
     * @return string
     */
    public function getFileContents( BinaryFile $binaryFile )
    {
        if ( empty( $binaryFile->uri ) || !is_string( $binaryFile->uri ) )
            throw new InvalidArgumentValue( "uri", $binaryFile->uri, "BinaryFile" );

        return $this->ioHandler->getFileContents( $binaryFile->uri );
    }

    /**
     * Generates SPI BinaryFileCreateStruct object from provided API BinaryFileCreateStruct object
     *
     * @param \eZ\Publish\API\Repository\Values\IO\BinaryFileCreateStruct $binaryFileCreateStruct
     *
     * @return \eZ\Publish\SPI\IO\BinaryFileCreateStruct
     */
    protected function buildSPIBinaryFileCreateStructObject( BinaryFileCreateStruct $binaryFileCreateStruct )
    {
        $spiBinaryCreateStruct = new SPIBinaryFileCreateStruct();

        $spiBinaryCreateStruct->path = $binaryFileCreateStruct->uri;
        $spiBinaryCreateStruct->size = $binaryFileCreateStruct->size;

        $mimeType = $binaryFileCreateStruct->contentType->type .
                    '/' .
                    $binaryFileCreateStruct->contentType->subType;

        $spiBinaryCreateStruct->mimeType = $mimeType;
        $spiBinaryCreateStruct->uri = $binaryFileCreateStruct->uri;
        $spiBinaryCreateStruct->originalFile = $binaryFileCreateStruct->originalFileName;
        $spiBinaryCreateStruct->setInputStream( $binaryFileCreateStruct->inputStream );

        return $spiBinaryCreateStruct;
    }

    /**
     * Generates API BinaryFile object from provided SPI BinaryFile object
     *
     * @param \eZ\Publish\SPI\IO\BinaryFile $spiBinaryFile
     *
     * @return \eZ\Publish\API\Repository\Values\IO\BinaryFile
     */
    protected function buildDomainBinaryFileObject( SPIBinaryFile $spiBinaryFile )
    {
        return new BinaryFile(
            array(
                //@todo is setting the id of file to path correct?
                'id' => $spiBinaryFile->path,
                'size' => $spiBinaryFile->size,
                'mtime' => $spiBinaryFile->mtime,
                'ctime' => $spiBinaryFile->ctime,
                'contentType' => new ContentType( $spiBinaryFile->mimeType ),
                'uri' => $spiBinaryFile->uri,
                'originalFile' => $spiBinaryFile->size
            )
        );
    }
}