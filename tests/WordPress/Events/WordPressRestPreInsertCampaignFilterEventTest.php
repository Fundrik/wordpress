<?php

declare(strict_types=1);

namespace Fundrik\WordPress\Tests\WordPress\Events;

use Fundrik\WordPress\Infrastructure\WordPress\Events\WordPressRestPreInsertCampaignFilterEvent;
use Fundrik\WordPress\Infrastructure\WordPress\WordPressContext\WordPressContextInterface;
use Fundrik\WordPress\Tests\MockeryTestCase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use stdClass;
use WP_REST_Request;

#[CoversClass( WordPressRestPreInsertCampaignFilterEvent::class )]
final class WordPressRestPreInsertCampaignFilterEventTest extends MockeryTestCase {

	private WP_REST_Request&MockInterface $request;
	private WordPressContextInterface&MockInterface $context;

	protected function setUp(): void {

		parent::setUp();

		$this->request = Mockery::mock( WP_REST_Request::class );
		$this->context = Mockery::mock( WordPressContextInterface::class );
	}

	#[Test]
	public function it_accepts_prepared_post_and_request(): void {

		$prepared_post = new stdClass();
		$prepared_post->post_title = 'Test Campaign';

		$event = new WordPressRestPreInsertCampaignFilterEvent( $prepared_post, $this->request, $this->context );

		$this->assertSame( $prepared_post, $event->prepared_post );
		$this->assertSame( 'Test Campaign', $event->prepared_post->post_title );
		$this->assertSame( $this->request, $event->request );
		$this->assertSame( $this->context, $event->context );
	}

	#[Test]
	public function it_allows_modification_of_prepared_post(): void {

		$prepared_post = new stdClass();

		$event = new WordPressRestPreInsertCampaignFilterEvent( $prepared_post, $this->request, $this->context );

		$event->prepared_post->post_content = 'Modified Content';

		$this->assertSame( 'Modified Content', $event->prepared_post->post_content );
	}
}
