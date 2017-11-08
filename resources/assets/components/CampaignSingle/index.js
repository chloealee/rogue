import React from 'react';
import PropTypes from 'prop-types';
import { map, find } from 'lodash';
import { RestApiClient } from '@dosomething/gateway';

import Post from '../Post';
import Empty from '../Empty';
import FilterBar from '../FilterBar';
import HistoryModal from '../HistoryModal';
import PagingButtons from '../PagingButtons';
import StatusCounter from '../StatusCounter';
import DropdownFilter from '../DropdownFilter';
import ModalContainer from '../ModalContainer';
import MultiValueFilter from '../MultiValueFilter';
import UserExport from '../UserExport';

class CampaignSingle extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      loadingNewPosts: false,
      filters: {
        status: 'accepted',
        tags: {
          'good-photo': {
            label: 'Good Photo',
            active: false,
          },
          'good-quote': {
            label: 'Good Quote',
            active: false,
          },
          'hide-in-gallery': {
            label: 'Hide In Gallery 👻',
            active: false,
          },
          'good-for-sponsor': {
            label: 'Good For Sponsor',
            active: false,
          },
          'good-for-storytelling': {
            label: 'Good For Storytelling',
            active: false,
          },
        },
      },
    };

    this.api = new RestApiClient();
    this.filterPosts = this.filterPosts.bind(this);
    this.getPostsByFilter = this.getPostsByFilter.bind(this);
    this.getPostsByPaginatedLink = this.getPostsByPaginatedLink.bind(this);
  }

  // Filter posts based on status or tag(s).
  filterPosts(filters) {
    this.setState({
      filters,
    });

    const formattedFilters = {
      campaign_id: this.props.campaign.id,
      status: filters.status,
    };

    // Grab all of the active tags to send to API request.
    if (filters.tags) {
      const activeTags = [];

      Object.keys(filters.tags).forEach((key) => {
        if (filters.tags[key].active === true) {
          activeTags.push(key);
        }
      });

      if (activeTags.length > 0) {
        formattedFilters.tag = activeTags.toString();
      }
    }

    this.getPostsByFilter(formattedFilters);
  }

  // Make API call to paginated link to get next/previous batch of posts.
  getPostsByPaginatedLink(url, event) {
    event.preventDefault();

    this.setState({ loadingNewPosts: true });

    // Strip the url to get query parameters.
    const splitEndpoint = url.split('/');
    const path = splitEndpoint.slice(-1)[0];
    const queryString = (path.split('?'))[1];

    this.api.get('posts', queryString)
      .then((json) => {
        this.setState({ loadingNewPosts: false });
        this.props.setNewPosts(json);
      });
  }

  // Make API call to GET /posts to get posts by filtered status and/or tag(s).
  getPostsByFilter(filters) {
    this.setState({ loadingNewPosts: true });

    this.api.get('/posts', {
      filter: filters,
      include: ['signup', 'siblings'],
    })
      .then((json) => {
        var arr = [];
        for (var i = 0; i < json.data.length; i++) {
            arr.push(json.data[i]);
        }
        const sortedResult = arr.sort(function(a, b) {
          return parseFloat(a.signup.data.quantity) - parseFloat(b.signup.data.quantity);
        });
        console.log(sortedResult);
        this.setState({ loadingNewPosts: false });
        this.props.setNewPosts(json);
      });

  }

  render() {
    const posts = this.props.posts;
    const campaign = this.props.campaign;
    const signups = this.props.signups;
    const tags = this.state.filters.tags;
    const tagFilters = {
      values: tags,
      type: 'tags',
    };

    const statusFilters = {
      values: {
        accepted: 'Accepted',
        pending: 'Pending',
        rejected: 'Rejected',
      },
      type: 'status',
      default: 'accepted',
    };

    return (
      <div className="container">
        <div className="container__block">
          <div className="container__row">
            <div className="container__block -half">
              <StatusCounter postTotals={this.props.post_totals} campaign={campaign} />
            </div>
            <div className="container__block -half">
              <UserExport campaign={campaign} />
            </div>
          </div>
        </div>
        <FilterBar onSubmit={this.filterPosts}>
          <DropdownFilter options={statusFilters} header={'Post Status'} />
          <MultiValueFilter options={tagFilters} header={'Tags'} />
        </FilterBar>

        <h2 className="heading -emphasized">Posts</h2>
        {this.props.loading || this.state.loadingNewPosts ?
          <div className="spinner" />
          :
          this.props.postIds.length !== 0 ?
            map(this.props.postIds, (key, value) => {
              const post = find(posts, { id: key });

              return (<Post
                key={key}
                post={post}
                user={signups[post.signup_id].user.data}
                signup={signups[post.signup_id]}
                campaign={campaign}
                onUpdate={this.props.updatePost}
                onTag={this.props.updateTag}
                deletePost={this.props.deletePost}
                showHistory={this.props.showHistory}
                rotate={this.props.rotate}
                showSiblings
                showQuantity
                allowHistory
              />);
            })
            :
            <Empty header="There are no results!" copy="Sorry, there are no posts that match your filters. Change or remove some tags and try again." />
        }

        <ModalContainer>
          {this.props.displayHistoryModal ?
            <HistoryModal
              id={this.props.historyModalId}
              onUpdate={this.props.updateQuantity}
              onClose={e => this.props.hideHistory(e)}
              campaign={campaign}
              signup={signups[posts[this.props.historyModalId].signup_id]}
              signupEvents={this.props.signupEvents}
            />
            : null}
        </ModalContainer>

        <PagingButtons onPaginate={this.getPostsByPaginatedLink} prev={this.props.prevPage} next={this.props.nextPage} />
      </div>
    );
  }
}

CampaignSingle.propTypes = {
  campaign: PropTypes.object.isRequired, // eslint-disable-line react/forbid-prop-types
  deletePost: PropTypes.func.isRequired,
  displayHistoryModal: PropTypes.bool,
  hideHistory: PropTypes.func.isRequired,
  historyModalId: PropTypes.number,
  loading: PropTypes.bool.isRequired,
  postIds: PropTypes.arrayOf(PropTypes.number),
  posts: PropTypes.object, // eslint-disable-line react/forbid-prop-types
  rotate: PropTypes.func.isRequired,
  setNewPosts: PropTypes.func.isRequired,
  showHistory: PropTypes.func.isRequired,
  signups: PropTypes.object, // eslint-disable-line react/forbid-prop-types
  signupEvents: PropTypes.array, // eslint-disable-line react/forbid-prop-types
  updatePost: PropTypes.func.isRequired,
  updateQuantity: PropTypes.func.isRequired,
  updateTag: PropTypes.func.isRequired,
};

CampaignSingle.defaultProps = {
  displayHistoryModal: null,
  historyModalId: null,
  postIds: null,
  posts: null,
  signups: null,
  signupEvents: null,
};

export default CampaignSingle;
